<?php
class ListingsController {
    
    private $excluded_properties = array();

    private function buildParams() {
        $params = array('fl' => $this->getFL(), 'supplier_id' => 223, 'pagesize' => 200);
        foreach($_GET as $param => $value) {
            switch($param) {

                //Allow us to filter out whichever properties we don't want
                //to come back in the search results
                case 'exclude': {
                    $exclusions = explode(',', $value);
                    $this->excluded_properties = $exclusions;
                    break;
                }
                case 'city': $params['location.city'] = $value; break;
                case 'state': $params['location.state'] = $value; break;
                case 'zip': $params['location.zip'] = $value; break;
                case 'product':
                    if($value == 'Select') {
                        $params['mobile_type'] = 'MoblSel';    
                    } else if($value == 'Exclusive') {
                        $params['mobile_type'] = 'MoblExcl';
                    }
                    break;

                case 'homesId': $params['id'] = $value; break;
                case 'latMax' : {
                    $lat = new stdClass();
                    $lat->min = $_GET['latMin'];
                    $lat->max = $_GET['latMax'];
                    $params['lat'] = $lat; 
                    break;
                }
                case 'lngMax' : {
                    $lng = new stdClass();
                    $lng->min = $_GET['lngMin'];
                    $lng->max = $_GET['lngMax'];
                    $params['lng'] = $lng; 
                    break;
                }
            }
        }

        return $params;
    }

    private function cleanResponse($response) {
        $fields = $this->getFields();

        $obj = json_decode($response);
        foreach($obj->listings as $index => $listing) {
            if(isset($listing->links)) {
                unset($obj->listings[$index]->links);
            }

            //Filter out whichever properties we don't want
            //to come back in the search results
            if(in_array($obj->listings[$index]->propid, $this->excluded_properties)){
                unset($obj->listings[$index]);
                continue;
            }

            foreach($fields as $homesName => $properName) {
                if($homesName != $properName && isset($listing->$homesName)) {
                    if($homesName == 'subdivision') {
                        $obj->listings[$index]->$homesName = ucwords(strtolower($listing->subdivision));
                    } else if($homesName == 'features_bullets') {
                        $obj->listings[$index]->$homesName = $obj->listings[$index]->$homesName->Bullet;
                    } else if($homesName == 'mobile_type') {
                        $value = '';
                        switch(strtolower($obj->listings[$index]->$homesName)) {
                            case 'moblsel': $value = 'Select'; break;
                            case 'moblexcl': $value = 'Exclusive'; break;
                            default: $value = strtolower($obj->listings[$index]->$homesName); break;
                        }
                        
                        $obj->listings[$index]->$homesName = $value;
                    }

                    $obj->listings[$index]->$properName = $obj->listings[$index]->$homesName;

                    unset($obj->listings[$index]->$homesName);
                } else if($properName == 'video' || $properName == 'product') { // Field doesn't show up if apartment doesn't have video
                    // $obj->listings[$index]->$properName = '';
                    unset($obj->listings[$index]->$properName);
                }
            }
        }

        $this->paginate($obj);

        return json_encode($obj);
    }

    private function getFields() {
        $fields = array(
            'propid' => 'homesId',
            'mls_number' => 'id',
            'lat' => 'lat',
            'lng' => 'lng',
            'zip' => 'zip',
            'subdivision' => 'name',
            'city' => 'city',
            'state' => 'state',
            'floorplans' => 'floorplans',
            'beds' => 'beds',
            'images' => 'images',
            'main_image' => 'primaryImage',
            'address' => 'streetAddress',
            'caption' => 'description',
            'property_video_url' => 'video',
            'mobile_type' => 'product',
            'detail_attributes' => 'management_info',
            'pet_policy' => 'pet_policy',
            'features_bullets' => 'features'
        );

        if(isset($_GET['fields'])) {
            $fields = array_intersect($fields, explode(',', $_GET['fields']));
        }

        return $fields;
    }

    private function getFL() {
        $fields = $this->getFields();
        $fl = '';
        foreach($fields as $homesIndex => $properName) {
            $fl .= $homesIndex .',';
        }

        return rtrim($fl, ',');
    }

    public function api() {
        $response = APIRequest::sendPost('listings/search', $this->buildParams());
        $response = $this->cleanResponse($response);

        header('Content-Type: application/json');
        echo $response;
        exit;
    }

    private function paginate($obj) {
        if(isset($_GET['limit']) && preg_match('/^\d+$/', $_GET['limit'])) {
            $start = (isset($_GET['start']) && preg_match('/^\d+$/', $_GET['start'])) ? $_GET['start'] : 0;
            $obj->listings = array_slice($obj->listings, $start, $_GET['limit']);
        }

        return $obj;
    }
}