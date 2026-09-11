<?php

class UserModel extends CI_Model
{

    public function checkDeliveryLocation($latitude, $longitude)
    {
        $get = $this->db->query("SELECT * FROM (SELECT *,(((acos(sin(('$latitude' * pi() / 180))*sin(( latitude * pi() / 180)) + cos(('$latitude' * pi() /180 ))*cos(( latitude * pi() / 180)) * cos((('$longitude' - longitude) * pi()/180))) ) * 180/pi()) * 60 * 1.1515 * 1.609344) AS distance FROM tbl_delivery_locations) tbl_delivery_locations WHERE distance <= '2' AND status = '1'");
        return $get->num_rows();
    }
}
