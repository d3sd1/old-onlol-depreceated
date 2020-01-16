<?php
/**
 * Created by PhpStorm.
 * User: andreigarcia
 * Date: 07/10/2018
 * Time: 15:31
 */

namespace App\Util;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ObjectManager
{
    private $encoders;
    private $normalizers;
    private $serializer;
    public function __construct()
    {
        $this->normalizers = array(new ObjectNormalizer());
        $this->encoders = array(new XmlEncoder(), new JsonEncoder());
        $this->serializer = new Serializer($this->normalizers, $this->encoders);
    }

    public function serialize($obj) {
        return $this->serializer->serialize($obj, 'json');
    }

    public function deserialize($data, $class) {
        $output = null;
        if(is_array($data)) {
            foreach($data as $dataEntry) {
                $output[] = $this->serializer->deserialize(json_encode($dataEntry), $class, 'json');
            }
        }
        else if(is_object($data)) {
            $data = json_encode($data);
        }

        if($output === null) {
            $output = $this->serializer->deserialize($data, $class, 'json');
        }
        return $output;
    }
}