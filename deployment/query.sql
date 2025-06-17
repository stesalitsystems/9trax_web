-- Table: stes.traker_positionaldata

-- DROP TABLE IF EXISTS stes.traker_positionaldata;

CREATE TABLE IF NOT EXISTS stes.traker_positionaldata
(
    id serial NOT NULL,
    parent_id integer NOT NULL,
    user_id integer NOT NULL,
    deviceid integer NOT NULL,
    latitude double precision NOT NULL,
    longitude double precision NOT NULL,
    lathsphere character varying(10) COLLATE pg_catalog."default",
    lonhsphere character varying(10) COLLATE pg_catalog."default",
    altitude double precision,
    noofsatellite character varying(10) COLLATE pg_catalog."default",
    trakerspeed double precision,
    currentdate date NOT NULL,
    currenttime time without time zone NOT NULL,
    headmovement double precision,
    recivestamp timestamp without time zone DEFAULT now(),
    protocolno character varying(10) COLLATE pg_catalog."default" DEFAULT NULL::character varying,
    batterystats character varying(10) COLLATE pg_catalog."default" DEFAULT NULL::character varying,
    misc character varying(100) COLLATE pg_catalog."default" NOT NULL DEFAULT '1'::character varying,
    group_id integer,
    geom geometry,
    sourcetype character varying(50) COLLATE pg_catalog."default",
    radius integer,
    devicestatus character varying COLLATE pg_catalog."default",
    temperature double precision,
    poleno character varying(255) COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS stes.traker_positionaldata
    OWNER to postgres;

-- Trigger: tt

-- DROP TRIGGER IF EXISTS tt ON stes.traker_positionaldata;

CREATE OR REPLACE TRIGGER tt
    BEFORE INSERT OR UPDATE 
    ON stes.traker_positionaldata
    FOR EACH ROW
    EXECUTE FUNCTION public.insert_into_multi_partition_table();

-- FUNCTION: public.clone_schema1(text, text)

-- DROP FUNCTION IF EXISTS public.clone_schema1(text, text);

CREATE OR REPLACE FUNCTION public.clone_schema1(
	source_schema text,
	dest_schema text)
    RETURNS void
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
    object text;
    create_table_query text;
    create_view_query text;
    create_sequence_query text;
    create_trigger_query text;
    create_constraint_query text;
    constraint_record RECORD;  -- Declare constraint_record as a RECORD type
    trigger_record RECORD;  
    buffer text; 
    constraint_name_ text;
    constraint_query_ text;   -- Declare trigger_record for triggers
    function_record RECORD;    -- Declare function_record for functions
    function_def text;  function_def1 text;       -- To store the function definition
BEGIN
    -- Create destination schema if it doesn't exist
    EXECUTE format('CREATE SCHEMA IF NOT EXISTS %I', dest_schema);
    CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA target_schema;
    PERFORM clone_functions_with_postgis1(source_schema, dest_schema);
    -- Clone tables
    FOR object IN
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = source_schema
          AND table_type = 'BASE TABLE'
    LOOP
        create_table_query := format(
            'CREATE TABLE %I.%I (LIKE %I.%I INCLUDING ALL)',
            dest_schema, object, source_schema, object
        );
        RAISE NOTICE 'Creating table: %', create_table_query;
        EXECUTE create_table_query;
    END LOOP;

    -- Clone sequences
    FOR object IN
        SELECT sequence_name
        FROM information_schema.sequences
        WHERE sequence_schema = source_schema
    LOOP
        create_sequence_query := format(
            'CREATE SEQUENCE %I.%I AS %s START WITH %s INCREMENT BY %s',
            dest_schema, object,
            (SELECT data_type FROM information_schema.sequences WHERE sequence_schema = source_schema AND sequence_name = object),
            (SELECT start_value FROM information_schema.sequences WHERE sequence_schema = source_schema AND sequence_name = object),
            (SELECT increment FROM information_schema.sequences WHERE sequence_schema = source_schema AND sequence_name = object) -- Corrected here
        );
        RAISE NOTICE 'Creating sequence: %', create_sequence_query;
        EXECUTE create_sequence_query;
    END LOOP;

    -- Clone views
    FOR object IN
        SELECT table_name
        FROM information_schema.views
        WHERE table_schema = source_schema
    LOOP
        create_view_query := format(
            'CREATE OR REPLACE VIEW %I.%I AS SELECT * FROM %I.%I',
            dest_schema, object, source_schema, object
        );
        RAISE NOTICE 'Creating view: %', create_view_query;
        EXECUTE create_view_query;
    END LOOP;

-- Clone triggers and their functions
FOR trigger_record IN
    SELECT trigger_name, event_object_table, action_timing, string_agg(event_manipulation, ' OR ') AS trigger_events, action_statement
    FROM information_schema.triggers
    WHERE event_object_schema = source_schema
    GROUP BY trigger_name, event_object_table, action_timing, action_statement
LOOP
    -- Declare variables inside the loop
    DECLARE
        trigger_function_name text;
        trigger_function_def text;
    BEGIN
        -- Extract the function name from the action statement using regex for EXECUTE FUNCTION
        trigger_function_name := substring(trigger_record.action_statement FROM 'EXECUTE FUNCTION\s+([^();]+)');

        -- Check if function name was extracted
        IF trigger_function_name IS NULL OR trigger_function_name = '' THEN
            RAISE WARNING 'Trigger function name is null or empty for trigger %', trigger_record.trigger_name;
            CONTINUE;
        END IF;

        -- Fetch the trigger function definition using pg_get_functiondef
        SELECT pg_get_functiondef(p.oid)
        INTO trigger_function_def
        FROM pg_proc p
        INNER JOIN pg_namespace n ON p.pronamespace = n.oid
        WHERE p.proname = trigger_function_name
        AND n.nspname = source_schema;

        -- Modify the function definition to reflect the new schema
        trigger_function_def := replace(trigger_function_def, format('%I.', source_schema), format('%I.', dest_schema));

        -- Execute the function definition to create the function in the new schema
        RAISE NOTICE 'Creating trigger function: %', trigger_function_def;
        EXECUTE trigger_function_def;

        -- Create the trigger using the modified action statement
        create_trigger_query := format(
            'CREATE TRIGGER %I %s %s ON %I.%I FOR EACH ROW %s',
            trigger_record.trigger_name,
            trigger_record.action_timing,
            trigger_record.trigger_events,
            dest_schema, trigger_record.event_object_table,
            replace(trigger_record.action_statement, source_schema || '.', dest_schema || '.')
        );

        -- Execute the trigger creation
        RAISE NOTICE 'Creating trigger: %', create_trigger_query;
        EXECUTE create_trigger_query;
    END;
END LOOP;

    
    -- Handle Unique Constraints
    FOR object IN
        SELECT table_name::text
        FROM information_schema.tables
        WHERE table_schema = source_schema
    LOOP
        buffer := dest_schema || '.' || object;

        FOR constraint_record IN
            SELECT con.conname AS constraint_name,
                   con.oid AS constraint_oid,
                   con.conkey AS conkey
            FROM pg_constraint con
            INNER JOIN pg_class cl ON con.conrelid = cl.oid
            INNER JOIN pg_namespace nsp ON nsp.oid = cl.relnamespace
            WHERE con.contype = 'u' -- Unique constraints
              AND cl.relname = object
              AND nsp.nspname = source_schema
        LOOP
            -- Get column names associated with the unique constraint
            DECLARE
                column_names TEXT;
            BEGIN
                -- Here, we use constraint_record instead of con
                SELECT STRING_AGG(attname, ', ') 
                INTO column_names
                FROM pg_attribute
                WHERE attrelid = constraint_record.constraint_oid
                  AND attnum = ANY (constraint_record.conkey) -- Use the correct variable here
                  AND NOT attisdropped;

                -- Check if column_names is empty before proceeding
                IF column_names IS NULL OR column_names = '' THEN
                    RAISE WARNING 'No columns found for unique constraint % on table %; skipping.', 
                                   constraint_record.constraint_name, object;
                    CONTINUE; -- Skip to the next iteration
                END IF;

                -- Check if the constraint already exists
                IF NOT EXISTS (
                    SELECT 1
                    FROM information_schema.table_constraints
                    WHERE table_schema = dest_schema
                      AND table_name = object
                      AND constraint_name = constraint_record.constraint_name
                ) THEN
                    -- Construct the ALTER TABLE statement
                    constraint_query_ := format(
                        'ALTER TABLE %I ADD CONSTRAINT %I UNIQUE (%s)',
                        buffer,
                        constraint_record.constraint_name,
                        column_names -- Use the dynamically retrieved column names
                    );
                    RAISE NOTICE 'Adding constraint: %', constraint_query_;
                    EXECUTE constraint_query_;
                ELSE
                    RAISE NOTICE 'Constraint % already exists, skipping.', constraint_record.constraint_name;
                END IF;
            END;
        END LOOP;
    END LOOP;

-- Clone Functions
--FOR function_record IN
  --  SELECT p.oid, p.proname, p.proargtypes, p.prosrc
  --  FROM pg_proc p
   -- INNER JOIN pg_namespace n ON p.pronamespace = n.oid
   -- WHERE n.nspname = source_schema
    --  AND p.prokind = 'f'  -- Filter only normal functions (not aggregates)
--LOOP
   -- function_def := pg_get_functiondef(function_record.oid);
    -- Modify the function definition to point to the new schema
  --  function_def := replace(function_def, 'CREATE FUNCTION', format('CREATE FUNCTION %I.', dest_schema) || 'CREATE FUNCTION');
    
    -- Update to replace RAISE NOTICE with -- RAISE NOTICE
   -- function_def1 := replace(function_def, 'RAISE NOTICE', '-- RAISE NOTICE');  
    
    -- RAISE NOTICE 'Creating function: %', function_def;  -- Commented out the RAISE statement
   -- EXECUTE function_def;
	--EXECUTE function_def1;
--END LOOP;

--PERFORM  create_hypertable(
   -- format('%I.%I', dest_schema, 'traker_positionaldata'),  -- table name
   -- 'currentdate',  -- time column
   -- 'deviceid',     -- space partitioning column
   -- number_partitions => 5,
   -- chunk_time_interval => INTERVAL '1 week'
--);
    RAISE NOTICE 'Schema % cloned to % successfully.', source_schema, dest_schema;  -- Ensure correct number of parameters

END;
$BODY$;

ALTER FUNCTION public.clone_schema1(text, text)
    OWNER TO postgres;



-- FUNCTION: public.clone_schema(text, text)

-- DROP FUNCTION IF EXISTS public.clone_schema(text, text);

CREATE OR REPLACE FUNCTION public.clone_schema(
	source_schema text,
	dest_schema text)
    RETURNS void
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
    object text;
    create_table_query text;
    create_view_query text;
    create_sequence_query text;
    create_trigger_query text;
    create_constraint_query text;
    constraint_record RECORD;  -- Declare constraint_record as a RECORD type
    trigger_record RECORD;  
    buffer text; 
    constraint_name_ text;
    constraint_query_ text;   -- Declare trigger_record for triggers
    function_record RECORD;    -- Declare function_record for functions
    function_def text;  function_def1 text;       -- To store the function definition
BEGIN
    -- Create destination schema if it doesn't exist
    EXECUTE format('CREATE SCHEMA IF NOT EXISTS %I', dest_schema);
    CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA target_schema;
    PERFORM clone_functions_with_postgis1(source_schema, dest_schema);
    -- Clone tables
    FOR object IN
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = source_schema
          AND table_type = 'BASE TABLE'
    LOOP
        create_table_query := format(
            'CREATE TABLE %I.%I (LIKE %I.%I INCLUDING ALL)',
            dest_schema, object, source_schema, object
        );
        RAISE NOTICE 'Creating table: %', create_table_query;
        EXECUTE create_table_query;
    END LOOP;

    -- Clone sequences
    FOR object IN
        SELECT sequence_name
        FROM information_schema.sequences
        WHERE sequence_schema = source_schema
    LOOP
        create_sequence_query := format(
            'CREATE SEQUENCE %I.%I AS %s START WITH %s INCREMENT BY %s',
            dest_schema, object,
            (SELECT data_type FROM information_schema.sequences WHERE sequence_schema = source_schema AND sequence_name = object),
            (SELECT start_value FROM information_schema.sequences WHERE sequence_schema = source_schema AND sequence_name = object),
            (SELECT increment FROM information_schema.sequences WHERE sequence_schema = source_schema AND sequence_name = object) -- Corrected here
        );
        RAISE NOTICE 'Creating sequence: %', create_sequence_query;
        EXECUTE create_sequence_query;
    END LOOP;

    -- Clone views
    FOR object IN
        SELECT table_name
        FROM information_schema.views
        WHERE table_schema = source_schema
    LOOP
        create_view_query := format(
            'CREATE OR REPLACE VIEW %I.%I AS SELECT * FROM %I.%I',
            dest_schema, object, source_schema, object
        );
        RAISE NOTICE 'Creating view: %', create_view_query;
        EXECUTE create_view_query;
    END LOOP;

-- Clone triggers and their functions
FOR trigger_record IN
    SELECT trigger_name, event_object_table, action_timing, string_agg(event_manipulation, ' OR ') AS trigger_events, action_statement
    FROM information_schema.triggers
    WHERE event_object_schema = source_schema
    GROUP BY trigger_name, event_object_table, action_timing, action_statement
LOOP
    -- Declare variables inside the loop
    DECLARE
        trigger_function_name text;
        trigger_function_def text;
    BEGIN
        -- Extract the function name from the action statement using regex for EXECUTE FUNCTION
        trigger_function_name := substring(trigger_record.action_statement FROM 'EXECUTE FUNCTION\s+([^();]+)');

        -- Check if function name was extracted
        IF trigger_function_name IS NULL OR trigger_function_name = '' THEN
            RAISE WARNING 'Trigger function name is null or empty for trigger %', trigger_record.trigger_name;
            CONTINUE;
        END IF;

        -- Fetch the trigger function definition using pg_get_functiondef
        SELECT pg_get_functiondef(p.oid)
        INTO trigger_function_def
        FROM pg_proc p
        INNER JOIN pg_namespace n ON p.pronamespace = n.oid
        WHERE p.proname = trigger_function_name
        AND n.nspname = source_schema;

        -- Modify the function definition to reflect the new schema
        trigger_function_def := replace(trigger_function_def, format('%I.', source_schema), format('%I.', dest_schema));

        -- Execute the function definition to create the function in the new schema
        RAISE NOTICE 'Creating trigger function: %', trigger_function_def;
        EXECUTE trigger_function_def;

        -- Create the trigger using the modified action statement
        create_trigger_query := format(
            'CREATE TRIGGER %I %s %s ON %I.%I FOR EACH ROW %s',
            trigger_record.trigger_name,
            trigger_record.action_timing,
            trigger_record.trigger_events,
            dest_schema, trigger_record.event_object_table,
            replace(trigger_record.action_statement, source_schema || '.', dest_schema || '.')
        );

        -- Execute the trigger creation
        RAISE NOTICE 'Creating trigger: %', create_trigger_query;
        EXECUTE create_trigger_query;
    END;
END LOOP;

    
    -- Handle Unique Constraints
    FOR object IN
        SELECT table_name::text
        FROM information_schema.tables
        WHERE table_schema = source_schema
    LOOP
        buffer := dest_schema || '.' || object;

        FOR constraint_record IN
            SELECT con.conname AS constraint_name,
                   con.oid AS constraint_oid,
                   con.conkey AS conkey
            FROM pg_constraint con
            INNER JOIN pg_class cl ON con.conrelid = cl.oid
            INNER JOIN pg_namespace nsp ON nsp.oid = cl.relnamespace
            WHERE con.contype = 'u' -- Unique constraints
              AND cl.relname = object
              AND nsp.nspname = source_schema
        LOOP
            -- Get column names associated with the unique constraint
            DECLARE
                column_names TEXT;
            BEGIN
                -- Here, we use constraint_record instead of con
                SELECT STRING_AGG(attname, ', ') 
                INTO column_names
                FROM pg_attribute
                WHERE attrelid = constraint_record.constraint_oid
                  AND attnum = ANY (constraint_record.conkey) -- Use the correct variable here
                  AND NOT attisdropped;

                -- Check if column_names is empty before proceeding
                IF column_names IS NULL OR column_names = '' THEN
                    RAISE WARNING 'No columns found for unique constraint % on table %; skipping.', 
                                   constraint_record.constraint_name, object;
                    CONTINUE; -- Skip to the next iteration
                END IF;

                -- Check if the constraint already exists
                IF NOT EXISTS (
                    SELECT 1
                    FROM information_schema.table_constraints
                    WHERE table_schema = dest_schema
                      AND table_name = object
                      AND constraint_name = constraint_record.constraint_name
                ) THEN
                    -- Construct the ALTER TABLE statement
                    constraint_query_ := format(
                        'ALTER TABLE %I ADD CONSTRAINT %I UNIQUE (%s)',
                        buffer,
                        constraint_record.constraint_name,
                        column_names -- Use the dynamically retrieved column names
                    );
                    RAISE NOTICE 'Adding constraint: %', constraint_query_;
                    EXECUTE constraint_query_;
                ELSE
                    RAISE NOTICE 'Constraint % already exists, skipping.', constraint_record.constraint_name;
                END IF;
            END;
        END LOOP;
    END LOOP;

-- Clone Functions
--FOR function_record IN
  --  SELECT p.oid, p.proname, p.proargtypes, p.prosrc
  --  FROM pg_proc p
   -- INNER JOIN pg_namespace n ON p.pronamespace = n.oid
   -- WHERE n.nspname = source_schema
    --  AND p.prokind = 'f'  -- Filter only normal functions (not aggregates)
--LOOP
   -- function_def := pg_get_functiondef(function_record.oid);
    -- Modify the function definition to point to the new schema
  --  function_def := replace(function_def, 'CREATE FUNCTION', format('CREATE FUNCTION %I.', dest_schema) || 'CREATE FUNCTION');
    
    -- Update to replace RAISE NOTICE with -- RAISE NOTICE
   -- function_def1 := replace(function_def, 'RAISE NOTICE', '-- RAISE NOTICE');  
    
    -- RAISE NOTICE 'Creating function: %', function_def;  -- Commented out the RAISE statement
   -- EXECUTE function_def;
	--EXECUTE function_def1;
--END LOOP;

--PERFORM  create_hypertable(
    --format('%I.%I', dest_schema, 'traker_positionaldata'),  -- table name
    --'currentdate',  -- time column
    --'deviceid',     -- space partitioning column
   -- number_partitions => 5,
   -- chunk_time_interval => INTERVAL '1 week'
--);
    RAISE NOTICE 'Schema % cloned to % successfully.', source_schema, dest_schema;  -- Ensure correct number of parameters

END;
$BODY$;

ALTER FUNCTION public.clone_schema(text, text)
    OWNER TO postgres;




-- FUNCTION: public.device_assignment(character varying, integer, integer, text)

-- DROP FUNCTION IF EXISTS public.device_assignment(character varying, integer, integer, text);

CREATE OR REPLACE FUNCTION public.device_assignment(
	p_serial_no_ character varying,
	p_user_id integer,
	p_group_id integer,
	p_operation text,
	OUT msg text)
    RETURNS text
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
    device_id INTEGER;
    assign_id INTEGER;
    user_schema VARCHAR;
    current_user_id INTEGER;
    current_parent_id INTEGER;
    current_group_id INTEGER;
    expected_group_ids INTEGER[] := ARRAY[2, 8, 5, 4, 3];
    level INTEGER := 1;
BEGIN
    -- Initialize output message
    msg := '';

    RAISE NOTICE 'Starting device_assignment with parameters: p_serial_no_=%, p_user_id=%, p_group_id=%, p_operation=%', p_serial_no_, p_user_id, p_group_id, p_operation;

    -- Check if device exists
    SELECT id INTO device_id
    FROM public.master_device_details
    WHERE serial_no = p_serial_no_;

    IF device_id IS NULL THEN
        msg := 'Device does not exist';
        RAISE NOTICE 'Device does not exist with serial_no=%', p_serial_no_;
        RETURN;
    ELSE
        RAISE NOTICE 'Device found: device_id=%', device_id;
    END IF;

    -- Get user schema, parent_id, and group_id
    SELECT schemaname, parent_id, group_id INTO user_schema, current_parent_id, current_group_id
    FROM public.user_login
    WHERE user_id = p_user_id;

    IF user_schema IS NULL THEN
        msg := 'User does not exist';
        RAISE NOTICE 'User does not exist with user_id=%', p_user_id;
        RETURN;
    ELSE
        RAISE NOTICE 'User found: user_schema=%, current_parent_id=%, current_group_id=%', user_schema, current_parent_id, current_group_id;
    END IF;

    IF LOWER(p_operation) = 'assign' THEN
        RAISE NOTICE 'Operation: ASSIGN';

        -- Check if device is already assigned
        SELECT id INTO assign_id
        FROM public.master_device_assign
        WHERE deviceid = device_id AND active = 1;

        IF assign_id IS NOT NULL THEN
            msg := 'Device already assigned to a user';
            RAISE NOTICE 'Device already assigned: assign_id=%', assign_id;
            RETURN;
        ELSE
            RAISE NOTICE 'Device is not assigned, proceeding with assignment';
        END IF;

        -- Start assigning device to users in hierarchy
        current_user_id := p_user_id;

        LOOP
            RAISE NOTICE 'Level %: current_user_id=%, current_parent_id=%, current_group_id=%', level, current_user_id, current_parent_id, current_group_id;

            -- Check if the group_id matches the expected group_id
            IF current_group_id <> expected_group_ids[level] THEN
                msg := 'Group ID mismatch at level ' || level;
                RAISE NOTICE 'Group ID mismatch at level %: expected_group_id=%, current_group_id=%', level, expected_group_ids[level], current_group_id;
                RETURN;
            ELSE
                RAISE NOTICE 'Group ID matches at level %', level;
            END IF;

            -- Insert into public.master_device_assign
            INSERT INTO public.master_device_assign(
                deviceid,
                parent_id,
                user_id,
                issudate,
                refunddate,
                active,
                issold,
                apply_scheam,
                group_id
            ) VALUES (
                device_id,
                current_parent_id,
                current_user_id,
                CURRENT_DATE,
                NULL,
                1,
                1,
                user_schema,
                current_group_id
            );
            RAISE NOTICE 'Inserted into public.master_device_assign: device_id=%, user_id=%', device_id, current_user_id;

            -- Insert into user-specific schema.master_device_assign
            EXECUTE format('INSERT INTO %I.master_device_assign(
                deviceid,
                parent_id,
                user_id,
                issudate,
                refunddate,
                active,
                issold,
                apply_scheam,
                group_id
            ) VALUES ($1, $2, $3, CURRENT_DATE, NULL::DATE, $4, $5, $6, $7)',
                user_schema)
            USING device_id, current_parent_id, current_user_id, 1, 1, user_schema, current_group_id;
            RAISE NOTICE 'Inserted into %.master_device_assign: device_id=%, user_id=%', user_schema, device_id, current_user_id;

            -- Move up the hierarchy
            current_user_id := current_parent_id;

            -- If no more parents or levels, exit the loop
            IF current_user_id IS NULL OR level >= array_length(expected_group_ids, 1) THEN
                RAISE NOTICE 'No more levels or parents, exiting loop';
                EXIT;
            END IF;

            -- Get next user's parent_id and group_id
            SELECT parent_id, group_id INTO current_parent_id, current_group_id
            FROM public.user_login
            WHERE user_id = current_user_id;

            level := level + 1;
        END LOOP;

        -- Update public.master_device_details
        UPDATE public.master_device_details
        SET warranty_date = CURRENT_DATE,
            assigned_to = p_user_id,
            superdevid = device_id,
            group_id = p_group_id
        WHERE id = device_id;
        RAISE NOTICE 'Updated public.master_device_details for device_id=%', device_id;

        -- Update user-specific schema.master_device_details
        EXECUTE format('UPDATE %I.master_device_details
            SET warranty_date = CURRENT_DATE,
                assigned_to = $1,
                superdevid = $2,
                group_id = $3
            WHERE id = $4',
            user_schema)
        USING p_user_id, device_id, p_group_id, device_id;
        RAISE NOTICE 'Updated %.master_device_details for device_id=%', user_schema, device_id;

        -- Additional code as per your request
        -- Check if the device exists in user-specific master_device_details
        EXECUTE format('SELECT superdevid FROM %I.master_device_details WHERE serial_no = $1', user_schema)
        INTO assign_id
        USING p_serial_no_;

        RAISE NOTICE 'Checked for device in %.master_device_details: assign_id=%', user_schema, assign_id;

        IF COALESCE(assign_id, 0) = 0 THEN
            RAISE NOTICE 'Device not found in %.master_device_details, inserting new record', user_schema;

            -- Insert into user_schema.master_device_details
            EXECUTE format('INSERT INTO %I.master_device_details(
                serial_no, mobile_no, mac_add, sdcard_no, active, linked, imei_no, sim_icc_id, warranty_date, insertby, updateby, inserttime, updatetime, assigned_to, superdevid, group_id, typeofdevice, dynamiccode, siminstalled, type
            )
            SELECT serial_no, mobile_no, mac_add, sdcard_no, active, linked, imei_no, sim_icc_id, warranty_date, insertby, updateby, inserttime, updatetime, assigned_to, superdevid, group_id, typeofdevice, dynamiccode, siminstalled, type
            FROM public.master_device_details WHERE id = $1', user_schema)
            USING device_id;

            RAISE NOTICE 'Inserted device into %.master_device_details', user_schema;
        ELSE
            RAISE NOTICE 'Device exists in %.master_device_details, updating record', user_schema;

            -- Update user_schema.master_device_details
            EXECUTE format('UPDATE %I.master_device_details
                SET warranty_date = CURRENT_DATE, assigned_to = $1, group_id = $2
                WHERE superdevid = $3', user_schema)
            USING p_user_id, p_group_id, device_id;

            RAISE NOTICE 'Updated device in %.master_device_details', user_schema;
        END IF;

        msg := 'Device successfully registered';

    ELSIF LOWER(p_operation) = 'unassign' THEN
        RAISE NOTICE 'Operation: UNASSIGN';

        -- Unassign the device in public.master_device_assign
        UPDATE public.master_device_assign
        SET active = 2
        WHERE deviceid = device_id AND active = 1;
        RAISE NOTICE 'Updated public.master_device_assign to set active=2 for device_id=%', device_id;

        -- Update public.master_device_details
        UPDATE public.master_device_details
        SET warranty_date = CURRENT_DATE,
            assigned_to = NULL,
            superdevid = NULL,
            group_id = NULL,
            active = 2
        WHERE id = device_id;
        RAISE NOTICE 'Updated public.master_device_details to unassign device_id=%', device_id;

        -- Update user-specific schema.master_device_assign
        EXECUTE format('UPDATE %I.master_device_assign
            SET active = 2
            WHERE deviceid = $1 AND active = 1',
            user_schema)
        USING device_id;
        RAISE NOTICE 'Updated %.master_device_assign to set active=2 for device_id=%', user_schema, device_id;

        -- Update user-specific schema.master_device_details
        EXECUTE format('UPDATE %I.master_device_details
            SET warranty_date = CURRENT_DATE,
                assigned_to = NULL,
                superdevid = NULL,
                group_id = NULL,
                active = 2
            WHERE id = $1',
            user_schema)
        USING device_id;
        RAISE NOTICE 'Updated %.master_device_details to unassign device_id=%', user_schema, device_id;

        msg := 'Device successfully unregistered';

    ELSE
        msg := 'Invalid operation';
        RAISE NOTICE 'Invalid operation: %', p_operation;
    END IF;

    RAISE NOTICE 'Procedure completed with message: %', msg;

END;
$BODY$;

ALTER FUNCTION public.device_assignment(character varying, integer, integer, text)
    OWNER TO postgres;



-- FUNCTION: public.device_assignment_details(character varying, integer, integer, integer)

-- DROP FUNCTION IF EXISTS public.device_assignment_details(character varying, integer, integer, integer);

CREATE OR REPLACE FUNCTION public.device_assignment_details(
	serial_no_ character varying,
	parent_id integer,
	user_id integer,
	group_id integer,
	OUT msg text)
    RETURNS text
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
  
DECLARE
ny_sql text;device_id integer;device_id_imi integer;group_id_ integer; user_id_ integer;depth_level integer;parent_id_ integer;user_scheam character varying;loop_distubtor_id integer;loop_company_id integer;loop_dept_id integer;loop_user_id integer;loop_distubtor_group_id integer;loop_company_group_id integer;loop_dept_group_id integer;loop_user_group_id integer;
loop_specal_user_group_id integer;loop_oem_group_id integer;loop_distubtor_parent_id integer;loop_company_parent_id integer;loop_parent_user_id integer;assign_id integer;
BEGIN
 
 
ny_sql:='SELECT id  FROM public.master_device_details where serial_no='''||serial_no_||'''';
 
execute ''||ny_sql||'' into device_id;
 
ny_sql:='SELECT schemaname  FROM public.user_login where  user_id='||user_id||' and group_id='||group_id||' and parent_id='||parent_id||' ';
 
execute ''||ny_sql||'' into user_scheam;
 
ny_sql:='select coalesce(id,0) from public.master_device_assign where group_id='||group_id||' and parent_id='||parent_id||' and user_id='||user_id||' and deviceid='||device_id||' and active=1';
 
execute ''||ny_sql||'' into assign_id;

if coalesce(assign_id,0)=0 then
ny_sql:='INSERT INTO public.master_device_assign(id, deviceid, parent_id, user_id, issudate, refunddate, active, 
            issold, apply_scheam, group_id) values(default,'||device_id||','||parent_id||', '||user_id||', current_date, null::date,2,1, '''||user_scheam||''', '||group_id||');';
           
execute ''||ny_sql||'';

ny_sql:='INSERT INTO public.tracker_device_movement(id, user_id, deviceid, parent_id, group_id, insertedon, updatedon, 
            active, refunddate, issudate, issold)
    values(default,'||user_id||','||device_id||','||parent_id||', '||group_id||', current_date, null::date,1,null::date,null::date,1)';
 
 execute ''||ny_sql||'';
ny_sql:='UPDATE public.master_device_details    SET  warranty_date=current_date,assigned_to='||user_id||',superdevid='||device_id||',group_id='||group_id||' WHERE id='||device_id||';';
 
execute ''||ny_sql||'';

	if user_scheam<>'public' then 
	 
	ny_sql:='INSERT INTO '||user_scheam||'.master_device_assign(id, deviceid, parent_id, user_id, issudate, refunddate, active, 
		    issold, apply_scheam, group_id) values(default,'||device_id||','||parent_id||', '||user_id||', current_date, null::date,2,1, '''||user_scheam||''', '||group_id||');';
	 
	execute ''||ny_sql||'';

	ny_sql:='select superdevid from '||user_scheam||'.master_device_details where  serial_no='''||serial_no_||'''::character varying';
	 
	execute ''||ny_sql||'' into assign_id;
			if coalesce(assign_id,0)=0 then
			ny_sql:='INSERT INTO '||user_scheam||'.master_device_details(serial_no, mobile_no, mac_add, sdcard_no, active, linked, 
				    imei_no, sim_icc_id, warranty_date, insertby, updateby, inserttime,updatetime, assigned_to, superdevid, group_id, typeofdevice,dynamiccode, siminstalled, type)
			select serial_no, mobile_no, mac_add, sdcard_no, active, linked,imei_no, sim_icc_id, warranty_date, insertby, updateby, inserttime,
				    updatetime, assigned_to, superdevid, group_id, typeofdevice,dynamiccode, siminstalled, type from public.master_device_details  where id='||device_id||'';
			 
			execute ''||ny_sql||'';
			else
			ny_sql:='UPDATE '||user_scheam||'.master_device_details    SET  warranty_date=current_date,assigned_to='||user_id||',group_id='||group_id||' WHERE superdevid='||device_id||'';
		 
			execute ''||ny_sql||'';

			end if;

	end if;

msg:='Device Successfully Registered';
else
msg:='Device Already Registered To User,Cannot be Register Now';
end if;

END;
$BODY$;

ALTER FUNCTION public.device_assignment_details(character varying, integer, integer, integer)
    OWNER TO postgres;





-- FUNCTION: public.cron_daly_trip_summary(timestamp without time zone, timestamp without time zone)

-- DROP FUNCTION IF EXISTS public.cron_daly_trip_summary(timestamp without time zone, timestamp without time zone);

CREATE OR REPLACE FUNCTION public.cron_daly_trip_summary(
	indatime_start timestamp without time zone,
	indatime_end timestamp without time zone)
    RETURNS void
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
  

DECLARE
ny_sql text;device_id integer;device_id_imi integer;group_id_ integer; user_id_ integer;depth_level integer;parent_id_ integer;user_scheam character varying;loop_distubtor_id integer;loop_company_id integer;loop_dept_id integer;loop_user_id integer;loop_distubtor_group_id integer;loop_company_group_id integer;loop_dept_group_id integer;loop_user_group_id integer;arr integer[];i integer;j  integer;abx text;abc text;
loop_specal_user_group_id integer;loop_oem_group_id integer;loop_distubtor_parent_id integer;loop_company_parent_id integer;loop_parent_user_id integer;assign_id integer;query_string text;
BEGIN



query_string:='select row_number() over() slno,* from(select distinct  deviceid::text from public.master_device_assign where apply_scheam='''||apply_scheam_name||''' and group_id=2 and active=1 and deviceid in (SELECT distinct deviceid
  FROM '||apply_scheam_name||'.traker_positionaldata  where currentdate ='''||$1||'''::date) order by deviceid asc)hgj' ;
abx='';abc='';
 FOR j,abc IN execute ''||query_string||''
 loop
 if abx='' then
 abx=abc;
 else
 abx=abx || ','||abc;
 end if;
--raise notice 'Divice id (%)',i;
end loop;
abx='''{'||abx||''||'}''::int[]';
--raise notice '%',abx;
query_string:='INSERT INTO public.trip_spesified_device(            divicename, result_date, deviceid, acting_trip, start_time, endtime,             duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop,             totalstoptime, polno, rd, dv, devicename, acting_triped, starttime,             end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop,             totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename,         polenameend) 
select (SELECT coalesce(serial_no,''Absent'') FROM public.master_device_details where id=dv) as divicename,rd as result_date , dv as deviceid,acting_triped as acting_trip,starttime as start_time,
end_time as endtime,duration1 as duration,distancecover as distance_cover,sosno as sos_no,alertno as alert_no ,callno as call_no,totalnoof_stop as totalnoofstop,totalsto_ptime as totalstoptime,pol_no as polno,
*,split_part(polename1,''#'',1) as polename,regexp_replace(polenameend1, ''^.*#'', '''') polenameend 
from (select result_date as rd , deviceid as dv,devicename ,row_number() over (partition by deviceid order by 1) as acting_triped,min(start_time) as starttime , max(end_time ) as end_time, sum(duration ) as duration1, sum(distance_cover) as distancecover, sum(sos_no) as sosno , sum(alert_no)as alertno , sum(call_no) as callno ,sum(totalnoofstop) as totalnoof_stop ,max(totalstoptime) as totalsto_ptime,string_agg(polno ,'' # '') as pol_no ,string_agg(polename ,''# '') as polename1 ,string_agg(polnoend ,'' # '') as polnoend ,string_agg(polenameend ,''#'') as polenameend1 
from public.get_report_trip_spesified_device_withpol('||abx||', '''||$1||''', '''||$2||''') where result_date is not null group by result_date , deviceid ,devicename,trip_details) hhj';

---raise notice '%',query_string;
---select distinct result_date  from  public.trip_spesified_device where result_date= and    
execute ''||query_string||'';
--raise notice 'Complite';
END;
$BODY$;

ALTER FUNCTION public.cron_daly_trip_summary(timestamp without time zone, timestamp without time zone)
    OWNER TO postgres;




-- FUNCTION: public.cron_daly_trip_summary(timestamp without time zone, timestamp without time zone, character varying)

-- DROP FUNCTION IF EXISTS public.cron_daly_trip_summary(timestamp without time zone, timestamp without time zone, character varying);

CREATE OR REPLACE FUNCTION public.cron_daly_trip_summary(
	indatime_start timestamp without time zone,
	indatime_end timestamp without time zone,
	apply_scheam_name character varying)
    RETURNS void
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
  

DECLARE
ny_sql text;device_id integer;device_id_imi integer;group_id_ integer; user_id_ integer;depth_level integer;parent_id_ integer;user_scheam character varying;loop_distubtor_id integer;loop_company_id integer;loop_dept_id integer;loop_user_id integer;loop_distubtor_group_id integer;loop_company_group_id integer;loop_dept_group_id integer;loop_user_group_id integer;arr integer[];i integer;j  integer;abx text;abc text;
loop_specal_user_group_id integer;loop_oem_group_id integer;loop_distubtor_parent_id integer;loop_company_parent_id integer;loop_parent_user_id integer;assign_id integer;query_string text;
BEGIN



query_string:='select row_number() over() slno,* from(select distinct  deviceid::text from public.master_device_assign where apply_scheam='''||apply_scheam_name||''' and group_id=2 and active=1 and deviceid in (SELECT distinct deviceid
  FROM '||apply_scheam_name||'.traker_positionaldata  where currentdate ='''||$1||'''::date) order by deviceid asc)hgj' ;
abx='';abc='';
 FOR j,abc IN execute ''||query_string||''
 loop
 if abx='' then
 abx=abc;
 else
 abx=abx || ','||abc;
 end if;
--raise notice 'Divice id (%)',i;
end loop;
abx='''{'||abx||''||'}''::int[]';
--raise notice '%',abx;
query_string:='INSERT INTO public.trip_spesified_device(            divicename, result_date, deviceid, acting_trip, start_time, endtime,             duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop,             totalstoptime, polno, rd, dv, devicename, acting_triped, starttime,             end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop,             totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename,         polenameend) 
select (SELECT coalesce(serial_no,''Absent'') FROM public.master_device_details where id=dv) as divicename,rd as result_date , dv as deviceid,acting_triped as acting_trip,starttime as start_time,
end_time as endtime,duration1 as duration,distancecover as distance_cover,sosno as sos_no,alertno as alert_no ,callno as call_no,totalnoof_stop as totalnoofstop,totalsto_ptime as totalstoptime,pol_no as polno,
*,split_part(polename1,''#'',1) as polename,regexp_replace(polenameend1, ''^.*#'', '''') polenameend 
from (select result_date as rd , deviceid as dv,devicename ,row_number() over (partition by deviceid order by 1) as acting_triped,min(start_time) as starttime , max(end_time ) as end_time, sum(duration ) as duration1, sum(distance_cover) as distancecover, sum(sos_no) as sosno , sum(alert_no)as alertno , sum(call_no) as callno ,sum(totalnoofstop) as totalnoof_stop ,max(totalstoptime) as totalsto_ptime,string_agg(polno ,'' # '') as pol_no ,string_agg(polename ,''# '') as polename1 ,string_agg(polnoend ,'' # '') as polnoend ,string_agg(polenameend ,''#'') as polenameend1 
from public.get_report_trip_spesified_device_withpol('||abx||', '''||$1||''', '''||$2||''') where result_date is not null group by result_date , deviceid ,devicename,trip_details) hhj';

---raise notice '%',query_string;
---select distinct result_date  from  public.trip_spesified_device where result_date= and    
execute ''||query_string||'';
--raise notice 'Complite';
END;
$BODY$;

ALTER FUNCTION public.cron_daly_trip_summary(timestamp without time zone, timestamp without time zone, character varying)
    OWNER TO postgres;



-- Table: public.device_scheduler

-- DROP TABLE IF EXISTS public.device_scheduler;

CREATE TABLE IF NOT EXISTS public.device_scheduler
(
    id serial NOT NULL,
    deviceid integer NOT NULL,
    deviceno character varying(20) COLLATE pg_catalog."default",
    expected_starttime timestamp without time zone,
    expected_endtime timestamp without time zone,
    expected_startpole character varying(20) COLLATE pg_catalog."default",
    expected_endpole character varying(20) COLLATE pg_catalog."default",
    trip_no integer,
    CONSTRAINT device_scheduler_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.device_scheduler
    OWNER to postgres;



    -- Table: public.trip_details_with_pole

-- DROP TABLE IF EXISTS public.trip_details_with_pole;

CREATE TABLE IF NOT EXISTS public.trip_details_with_pole
(
    id serial NOT NULL,
    result_date date,
    deviceid integer,
    start_time time without time zone,
    end_time time without time zone,
    duration interval,
    distance_cover double precision,
    devicename text COLLATE pg_catalog."default",
    expected_starttime time without time zone,
    expected_endtime time without time zone,
    expected_startpole text COLLATE pg_catalog."default",
    expected_endpole text COLLATE pg_catalog."default",
    start_pole_name text COLLATE pg_catalog."default",
    end_pole_name text COLLATE pg_catalog."default",
    start_batterystats double precision,
    end_batterystats double precision,
    expected_distance double precision,
    device_serial character varying COLLATE pg_catalog."default",
    CONSTRAINT trip_details_with_pole_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.trip_details_with_pole
    OWNER to postgres;



    -- Table: public.tbl_daily_report_snapshot

-- DROP TABLE IF EXISTS public.tbl_daily_report_snapshot;

CREATE TABLE IF NOT EXISTS public.tbl_daily_report_snapshot
(
    report_id serial NOT NULL,
    report_name character varying(255) COLLATE pg_catalog."default",
    dttime time without time zone,
    usertype character varying(20) COLLATE pg_catalog."default",
    pway character varying(30) COLLATE pg_catalog."default",
    section character varying(30) COLLATE pg_catalog."default",
    device_off text COLLATE pg_catalog."default",
    beats_covered text COLLATE pg_catalog."default",
    beats_not_covered text COLLATE pg_catalog."default",
    overspeed text COLLATE pg_catalog."default",
    excel character varying(1000) COLLATE pg_catalog."default",
    pdf character varying(1000) COLLATE pg_catalog."default",
    dt date DEFAULT now(),
    CONSTRAINT tbl_daily_report_snapshot_pkey PRIMARY KEY (report_id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.tbl_daily_report_snapshot
    OWNER to postgres;



    -- Table: srde.tbl_device_schedule_updated

-- DROP TABLE IF EXISTS srde.tbl_device_schedule_updated;

CREATE TABLE IF NOT EXISTS srde.tbl_device_schedule_updated
(
    schedule_id serial NOT NULL,
    imeino character varying(20) COLLATE pg_catalog."default",
    usertype character varying(15) COLLATE pg_catalog."default",
    stpole character varying(50) COLLATE pg_catalog."default",
    endpole character varying(50) COLLATE pg_catalog."default",
    sttime time without time zone,
    endtime time without time zone,
    distance_travelled double precision,
    stpolelat double precision,
    stpolelon double precision,
    endpolelat double precision,
    endpolelon double precision,
    trip integer,
    stgeom geometry,
    endgeom geometry,
    devicename character varying(255) COLLATE pg_catalog."default",
    pwi_id integer,
    section_id integer,
    status integer DEFAULT 1,
    CONSTRAINT tbl_device_schedule_updated_pkey PRIMARY KEY (schedule_id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS srde.tbl_device_schedule_updated
    OWNER to postgres;





-- Table: stes.trip_schedule

-- DROP TABLE IF EXISTS stes.trip_schedule;

CREATE TABLE IF NOT EXISTS stes.trip_schedule
(
    schedule_id serial NOT NULL,
    deviceid integer NOT NULL,
    pwi_id integer,
    section_id integer,
    trip_name character varying(50) COLLATE pg_catalog."default",
    imeino character varying(20) COLLATE pg_catalog."default",
    expected_start_date date NOT NULL,
    expected_start_time time without time zone NOT NULL,
    expected_end_date date NOT NULL,
    expected_end_time time without time zone NOT NULL,
    active boolean DEFAULT true,
    device_type character varying(20) COLLATE pg_catalog."default",
    devicename character varying(255) COLLATE pg_catalog."default",
    CONSTRAINT trip_schedule_pkey PRIMARY KEY (schedule_id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS stes.trip_schedule
    OWNER to postgres;



-- Table: stes.trip_schedule_details

-- DROP TABLE IF EXISTS stes.trip_schedule_details;

CREATE TABLE IF NOT EXISTS stes.trip_schedule_details
(
    schedule_details_id serial NOT NULL,
    schedule_id integer NOT NULL,
    expected_stpole character varying COLLATE pg_catalog."default",
    expected_stlat double precision,
    expected_stlon double precision,
    expected_start_datetime timestamp without time zone NOT NULL,
    actual_stpole character varying COLLATE pg_catalog."default",
    actual_stlat double precision,
    actual_stlon double precision,
    actual_start_datetime timestamp without time zone,
    expected_endpole character varying COLLATE pg_catalog."default",
    expected_endlat double precision,
    expected_endlon double precision,
    expected_end_datetime timestamp without time zone NOT NULL,
    actual_endpole character varying COLLATE pg_catalog."default",
    actual_endlat double precision,
    actual_endlon double precision,
    actual_end_datetime timestamp without time zone,
    trip_status character varying(20) COLLATE pg_catalog."default",
    delay_minutes integer DEFAULT 0,
    max_speed integer,
    total_distance double precision,
    expected_distance double precision,
    trip_no integer,
    CONSTRAINT trip_schedule_details_pkey PRIMARY KEY (schedule_details_id),
    CONSTRAINT trip_schedule_details_schedule_id_fkey FOREIGN KEY (schedule_id)
        REFERENCES stes.trip_schedule (schedule_id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE CASCADE,
    CONSTRAINT trip_schedule_details_trip_status_check CHECK (trip_status::text = ANY (ARRAY['Not Started'::character varying::text, 'Ongoing'::character varying::text, 'Completed'::character varying::text, 'Delayed'::character varying::text, 'Failed'::character varying::text]))
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS stes.trip_schedule_details
    OWNER to postgres;




-- Table: stes.tbl_trip

-- DROP TABLE IF EXISTS stes.tbl_trip;

CREATE TABLE IF NOT EXISTS stes.tbl_trip
(
    trip_id serial NOT NULL,
    deviceid integer,
    imeino character varying(20) COLLATE pg_catalog."default",
    startdate date,
    starttime time without time zone,
    bufferstdate date,
    buffersttime time without time zone,
    stpole character varying COLLATE pg_catalog."default",
    endpole character varying COLLATE pg_catalog."default",
    enddate date,
    bufferenddate date,
    bufferendtime time without time zone,
    totaldistancetravel double precision,
    timetravelled interval,
    stlat double precision,
    stlon double precision,
    endlat double precision,
    endlon double precision,
    sttimestamp timestamp without time zone,
    endtimestamp timestamp without time zone,
    endtime time without time zone,
    startbattery double precision,
    endbattery double precision,
    beats_covered text COLLATE pg_catalog."default",
    speed integer,
    remarks character varying(500) COLLATE pg_catalog."default",
    speed_samples integer,
    avg_speed double precision,
    max_speed double precision,
    distance_travelled double precision,
    schedule_details_id integer,
    CONSTRAINT tbl_trip_pkey PRIMARY KEY (trip_id),
    CONSTRAINT tbl_trip_schedule_details_id_fkey FOREIGN KEY (schedule_details_id)
        REFERENCES stes.trip_schedule_details (schedule_details_id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS stes.tbl_trip
    OWNER to postgres;

COMMENT ON COLUMN stes.tbl_trip.deviceid
    IS 'For legacy reasons can''t be linked as foreign key.';

COMMENT ON COLUMN stes.tbl_trip.bufferstdate
    IS 'When the device comes in vicinity of a pole for the first time.';

COMMENT ON COLUMN stes.tbl_trip.buffersttime
    IS 'When the device comes in vicinity of a pole for the first time.';

COMMENT ON COLUMN stes.tbl_trip.bufferenddate
    IS 'When the device comes in vicinity of a pole for the last time.';

COMMENT ON COLUMN stes.tbl_trip.bufferendtime
    IS 'When the device comes in vicinity of a pole for the last time.';

COMMENT ON COLUMN stes.tbl_trip.stlat
    IS 'Start Latitude';

COMMENT ON COLUMN stes.tbl_trip.stlon
    IS 'Start Longitude';
-- Index: idx_trip_deviceid

-- DROP INDEX IF EXISTS stes.idx_trip_deviceid;

CREATE INDEX IF NOT EXISTS idx_trip_deviceid
    ON stes.tbl_trip USING btree
    (deviceid ASC NULLS LAST)
    TABLESPACE pg_default;
-- Index: idx_trip_endtimestamp

-- DROP INDEX IF EXISTS stes.idx_trip_endtimestamp;

CREATE INDEX IF NOT EXISTS idx_trip_endtimestamp
    ON stes.tbl_trip USING btree
    (endtimestamp ASC NULLS LAST)
    TABLESPACE pg_default;
-- Index: idx_trip_tripid

-- DROP INDEX IF EXISTS stes.idx_trip_tripid;

CREATE INDEX IF NOT EXISTS idx_trip_tripid
    ON stes.tbl_trip USING btree
    (trip_id ASC NULLS LAST)
    TABLESPACE pg_default;




-- Table: public.tbl_trip_stoppage

-- DROP TABLE IF EXISTS public.tbl_trip_stoppage;

CREATE TABLE IF NOT EXISTS public.tbl_trip_stoppage
(
    stoppage_id serial NOT NULL,
    trip_id integer,
    pole character varying(255) COLLATE pg_catalog."default",
    stoppage_start timestamp without time zone,
    stoppage_duration interval,
    imeino character varying(20) COLLATE pg_catalog."default",
    CONSTRAINT tbl_trip_stoppage_pkey PRIMARY KEY (stoppage_id),
    CONSTRAINT tbl_trip_stoppage_trip_id_fkey FOREIGN KEY (trip_id)
        REFERENCES stes.tbl_trip (trip_id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.tbl_trip_stoppage
    OWNER to postgres;



