<?php


class PodsEventSubscriberRepository
{
    private $pods;

    public function __construct()
    {
        $this->pods = pods('event_subscriber');
    }

    public function addSubscriber(string $email, string $phone , int $people , string $event , string $name)
    {
        $data = [
            'email' => $email,
            'phone' => $phone,
            'people' => $people,
            'event' => $event,
            'name' => $name
        ];

       $this->addPods($data);
    }


    private function addPods(array $data)
    {
        return $this->pods->add($data);

    }

    public function existSubscriberInEvent(string $event, string $email)
    {
        $params = [
            'where'=>" email ='".$email."' AND event='".$event."'"
        ];

        $podsResult = $this->pods->find($params);
        $this->pods->filters($params);

        if(empty($podsResult->fetch())) {
            return false;
        }

        return true;

    }

}