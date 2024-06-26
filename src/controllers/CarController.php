<?php

class CarController
{
    public static function root(): string
    {
        return json_encode(
            [
                'code' => 200,
                'Available endpoints' => [
                    'GET' => [
                        '/cars/' => 'Fetch all cars from database',
                        '/cars/:id/' => 'Fetch car by id',
                        '/cars_by_make/:make/' => 'Fetch cars by Make',
                        '/cars_by_model/:model/' => 'Fetch cars by Model',
                        '/cars_by_color/:color/' => 'Fetch cars by Color',
                    ],
                    'POST' => [
                        '/cars/' => 'Save new car to the database',
                    ],
                    'PUT' => [
                        '/cars/:id/' => 'Override car data in database',
                    ],
                    'DELETE' => [
                        '/cars/:id/' => 'Delete car from database',
                    ]
                ]
            ]
        );
    }

    public static function getAllCars(): string
    {
        $url = UrlRoute::getUrl();
        try {
            if (array_key_exists(2, $url) && array_key_exists(3, $url)) {
                $cars = Car::findAll(filter: $url[2], value: $url[3]);
            } else {
                $cars = Car::findAll();
            }
        } catch (Exception $e) {
            UrlRoute::error(message: $e->getMessage(), code: $e->getCode());
        }
        $jsonData = [
            'code' => 200,
            'data' => []
        ];
        foreach ($cars as $car) {
            array_push($jsonData['data'], $car->getData());
        }
        return json_encode($jsonData);
    }

    public static function getCar(): string
    {
        $url = UrlRoute::getUrl();
        if (array_key_exists(2, $url)) {
            if (!is_numeric($url[2])) {
                UrlRoute::error('Invalid id parameter type.', code: 422);
            }
            $id = (int)$url[2];
            try {
                $jsonData = [
                    'code' => 200,
                    'data' => Car::find(id: $id)->getData()
                ];
            } catch (Exception $e) {
                UrlRoute::error($e->getMessage(), code: 404);
            }
        }
        return json_encode($jsonData);
    }

    public static function deleteCar(): string
    {
        $url = UrlRoute::getUrl();
        if (array_key_exists(2, $url)) {
            if (!is_numeric($url[2])) {
                UrlRoute::error('Invalid id parameter type.', code: 422);
            }
            $id = (int)$url[2];
            try {
                $car = Car::find(id: $id)->delete();;
            } catch (Exception $e) {
                UrlRoute::error($e->getMessage(), code: 404);
            }
            $jsonData = [
                'code' => 204,
                'data' => "Car on index " . $car->getId() . " deleted."
            ];
        }
        return json_encode($jsonData);
    }
}
