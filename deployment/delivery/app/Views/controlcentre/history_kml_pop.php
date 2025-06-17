<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }
    
    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
    
    tr:nth-child(even) {
        background-color: #dddddd;
    }
    
    .btn_dtl{
        background: none repeat scroll 0% 0% #4C9ED9;
        padding: 5px 10px;
        color: #FFF;
        text-decoration: none;
        border-radius: 3px;
        font-size: 0.88em;
        display: inline-block;
    }
    .disp_head {
        background: none repeat scroll 0% 0% #4C9ED9;
        /*background: rgba(15, 146, 189, 1);*/
        font-size: 1em;
        color: #FFF;
        font-weight: bold;
        text-align: center;
        border: none;
    }
</style>

<center>
<div style="height: 250px; overflow-y: scroll;">
    <table width="100%" border="1">        
        <tr>
            <td width="50%">Device</td>
            <td width="50%"><?php echo $deviceNo; ?></td>
        </tr>
        <tr>
            <td width="50%">From Date Time</td>
            <td width="50%"><?php echo $from_datetime; ?></td>
        </tr>
        <tr>
            <td width="50%">To Date Time</td>
            <td width="50%"><?php echo $to_datetime; ?></td>
        </tr>
    </table>
</div>
</center>