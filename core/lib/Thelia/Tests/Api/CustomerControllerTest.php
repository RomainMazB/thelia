<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Thelia\Tests\Api;

use Thelia\Tests\ApiTestCase;


/**
 * Class CustomerControllerTest
 * @package Thelia\Tests\Api
 * @author Manuel Raynaud <mraynaud@openstudio.fr>
 */
class CustomerControllerTest extends ApiTestCase
{

    /**
     * @covers \Thelia\Controller\Api\CustomerController::listAction
     */
    public function testListActionWithDefaultParameters()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/customers?sign='.$this->getSignParameter(""),[],[],
            $this->getServerParameters()
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Http status code must be 200');
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(10, $content);

        $this->customerKeyTest($content[0]);
    }

    /**
     * @covers \Thelia\Controller\Api\CustomerController::listAction
     */
    public function testListActionWithOrderError()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/customers?order=foo&sign='.$this->getSignParameter(""),[],[],
            $this->getServerParameters()
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'Http status code must be 400');

    }

    /**
     * @covers \Thelia\Controller\Api\CustomerController::listAction
     */
    public function testListActionWithLimit()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/customers?limit=1&sign='.$this->getSignParameter(""),[],[],
            $this->getServerParameters()
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Http status code must be 200');
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(1, $content);

        $this->customerKeyTest($content[0]);
    }

    protected function customerKeyTest($customer)
    {
        $this->assertArrayHasKey('Id', $customer, 'customer entity must contains Id key');
        $this->assertArrayHasKey('Ref', $customer, 'customer entity must contains Ref key');
        $this->assertArrayHasKey('TitleId', $customer, 'customer entity must contains TitleId key');
        $this->assertArrayHasKey('Firstname', $customer, 'customer entity must contains Firstname key');
        $this->assertArrayHasKey('Lastname', $customer, 'customer entity must contains Lastname key');
        $this->assertArrayHasKey('Email', $customer, 'customer entity must contains Email key');
        $this->assertArrayHasKey('Reseller', $customer, 'customer entity must contains Reseller key');
        $this->assertArrayHasKey('Lang', $customer, 'customer entity must contains Lang key');
        $this->assertArrayHasKey('Sponsor', $customer, 'customer entity must contains Sponsor key');
        $this->assertArrayHasKey('Discount', $customer, 'customer entity must contains Discount key');
        $this->assertArrayHasKey('CreatedAt', $customer, 'customer entity must contains CreatedAt key');
        $this->assertArrayHasKey('UpdatedAt', $customer, 'customer entity must contains UpdatedAt key');
    }

    /**
     * @covers \Thelia\Controller\Api\CustomerController::getAction
     */
    public function testGetAction()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/customers/1?&sign='.$this->getSignParameter(""),[],[],
            $this->getServerParameters()
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Http status code must be 200');

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(1, $content);

        $this->customerKeyTest($content[0]);
    }

    /**
     * @covers \Thelia\Controller\Api\CustomerController::getAction
     */
    public function testGetActionWithUnexistingCustomer()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/customers/'.PHP_INT_MAX.'?&sign='.$this->getSignParameter(""),[],[],
            $this->getServerParameters()
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'Http status code must be 404');
    }

    public function testCreate()
    {
        $user = [
            'thelia_customer_create' => [
                'title' => 1,
                'firstname' => 'Thelia',
                'lastname'  => 'Thelia',
                'address1'  => 'street address 1',
                'city'      => 'Clermont-Ferrand',
                'zipcode'   => 63100,
                'country'   => 64,
                'email'     => sprintf("%s@thelia.fr", uniqid()),
                'password'  => 'azerty',
                'lang'      => 1
            ]
        ];

        $requestContent = json_encode($user);

        $client = static::createClient();
        $servers = $this->getServerParameters();
        $servers['CONTENT_TYPE'] = 'application/json';
        $client->request(
            'POST',
            '/api/customers?&sign='.$this->getSignParameter($requestContent),[],[],
            $servers,
            $requestContent
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

    }
}