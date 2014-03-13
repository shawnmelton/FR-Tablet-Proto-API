<?php
class ListingsController {
    private function buildParams() {
        $params = array('fl' => $this->getFL());
        foreach($_GET as $param => $value) {
            switch($param) {
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
                case 'limit': $params['pagesize'] = intval($value); break;
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

            foreach($fields as $homesName => $properName) {
                if($homesName != $properName && isset($listing->$homesName)) {
                    if($homesName == 'subdivision') {
                        $obj->listings[$index]->$homesName = ucwords(strtolower($listing->subdivision));
                    }

                    $obj->listings[$index]->$properName = $obj->listings[$index]->$homesName;

                    unset($obj->listings[$index]->$homesName);
                }
            }
        }

        return json_encode($obj);
    }

    private function getFields() {
        return array(
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
            'address' => 'streetAddress'
        );
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
        $response = APIRequest::send('listings/search', $this->buildParams());
        $response = $this->cleanResponse($response);

        header('Content-Type: application/json');
        echo $response;
        exit;
    }
}