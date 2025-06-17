<?php

namespace App\Libraries;

class Notification
{
    private $title;
    private $message;
    private $image_url;
    private $action;
    private $action_destination;
    private $data;

    public function __construct()
    {
        // Initialization if needed
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setImage($imageUrl)
    {
        $this->image_url = $imageUrl;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function setActionDestination($actionDestination)
    {
        $this->action_destination = $actionDestination;
    }

    public function setPayload($data)
    {
        $this->data = $data;
    }

    public function getNotification()
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'image' => $this->image_url,
            'action' => $this->action,
            'action_destination' => $this->action_destination,
        ];
    }
}
