<?php

namespace Lthrt\EntityBundle\Tests\Controller;

use Lthrt\EntityBundle\Controller\EntityController;
use Lthrt\EntityBundle\Tests\LoadFixturesFromMetadata;
use Lthrt\EntityBundle\Tests\TestWithoutReboot;
use Symfony\Component\HttpFoundation\Request;

class EntityControllerTest extends TestWithoutReboot
{
    use LoadFixturesFromMetadata;

    private $controller;

    public function tearDown()
    {
        // parent::tearDown();
    }

    public function setUp()
    {
        parent::setUp();
        $this->controller = new EntityController();
        $this->controller->setContainer(static::$kernel->getContainer());
    }

    public function testIsThisThingOn()
    {
        $this->assertTrue(true);
    }

    public function testEditAction()
    {
        static::loadFixtures(static::$kernel->getContainer()->get('doctrine')->getManager());
        $request = new Request();

        try {
            $response = $this->controller->editAction($request, 'Unaliased');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
            $this->assertContains('Unspecified class alias used in generic routing', $e->getMessage());
        }

        $response = $this->controller->editAction($request, 'GenericEntity', 1);
        $this->assertContains('<form name="form" method="post" action="/_GenericEntity/mod/1/">', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());

        $response = $this->controller->editAction($request, 'GenericEntity');
        // symfony auto converts post into put under the hood, so form is rendered as post
        $this->assertContains('<form name="form" method="post" action="/_GenericEntity/create/">', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
    }

    public function testJsonAction()
    {
        static::loadFixtures(static::$kernel->getContainer()->get('doctrine')->getManager());
        $request = new Request();

        try {
            $response = $this->controller->jsonAction($request, 'Unaliased');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
            $this->assertContains('Unspecified class alias used in generic routing', $e->getMessage());
        }
        try {
            $response = $this->controller->jsonAction($request, 'Unaliased', "1,2");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
            $this->assertContains('Unspecified class alias used in generic routing', $e->getMessage());
        }

        $response = $this->controller->jsonAction($request, 'GenericEntity', "11,12");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertContains('"data":null', $response->getContent());

        $response = $this->controller->jsonAction($request, 'GenericEntity', 1);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertContains('"fluff":"Fluff"', $response->getContent());
        $this->assertContains('"name":"Test"', $response->getContent());
        $this->assertContains('"id":1', $response->getContent());

        $response = $this->controller->jsonAction($request, 'GenericEntity', 2);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertContains('"fluff":"Stuff"', $response->getContent());
        $this->assertContains('"name":"Check"', $response->getContent());
        $this->assertContains('"id":2', $response->getContent());

        $response = $this->controller->jsonAction($request, 'GenericEntity', "1,2");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertContains('"fluff":"Fluff"', $response->getContent());
        $this->assertContains('"name":"Test"', $response->getContent());
        $this->assertContains('"id":1', $response->getContent());
        $this->assertContains('"fluff":"Stuff"', $response->getContent());
        $this->assertContains('"name":"Check"', $response->getContent());
        $this->assertContains('"id":2', $response->getContent());
        $this->assertContains('[', $response->getContent());
        $this->assertContains(']', $response->getContent());

        $response = $this->controller->jsonAction($request, 'GenericEntity');
        // symfony auto converts post into put under the hood, so form is rendered as post
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertContains('"fluff":"Fluff"', $response->getContent());
        $this->assertContains('"name":"Test"', $response->getContent());
        $this->assertContains('"id":1', $response->getContent());
        $this->assertContains('"fluff":"Stuff"', $response->getContent());
        $this->assertContains('"name":"Check"', $response->getContent());
        $this->assertContains('"id":2', $response->getContent());
        $this->assertContains('[', $response->getContent());
        $this->assertContains(']', $response->getContent());

        $response = $this->controller->jsonAction($request, 'GenericJsonEntity', 1);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertContains('"id":1', $response->getContent());
        $this->assertContains('"name":"test"', $response->getContent());
        $this->assertContains('"fluff":"fluff"', $response->getContent());

        $response = $this->controller->jsonAction($request, 'GenericJsonEntity', 2);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertContains('"id":2', $response->getContent());
        $this->assertContains('"name":"check"', $response->getContent());
        $this->assertContains('"fluff":"stuff"', $response->getContent());

        $response = $this->controller->jsonAction($request, 'GenericJsonEntity', "1,2");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertContains('"id":2', $response->getContent());
        $this->assertContains('"name":"check"', $response->getContent());
        $this->assertContains('"fluff":"stuff"', $response->getContent());

        $response = $this->controller->jsonAction($request, 'GenericJsonEntity');
        // symfony auto converts post into put under the hood, so form is rendered as post
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertContains('"id":1', $response->getContent());
        $this->assertContains('"name":"test"', $response->getContent());
        $this->assertContains('"fluff":"fluff"', $response->getContent());
        $this->assertContains('"id":2', $response->getContent());
        $this->assertContains('"name":"check"', $response->getContent());
        $this->assertContains('"fluff":"stuff"', $response->getContent());
        $this->assertContains('[', $response->getContent());
        $this->assertContains(']', $response->getContent());
    }

    public function testLogAction()
    {
        // id mandatory in this action
        static::loadFixtures(static::$kernel->getContainer()->get('doctrine')->getManager());
        $request = new Request();

        try {
            $response = $this->controller->logAction($request, 'Unaliased', 1);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
            $this->assertContains('Unspecified class alias used in generic routing', $e->getMessage());
        }

        $response = $this->controller->logAction($request, 'TestEntity1', 1);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertContains('{"data":null}', $response->getContent());

        $response = $this->controller->logAction($request, 'GenericLoggedEntity', 1);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertEquals(4, count(json_decode($response->getContent(), true)['data']));
        $this->assertContains("More Fluff", $response->getContent());
        $this->assertContains("MOAR Fluff", $response->getContent());
        $this->assertContains("MOAR cuz MOAR Fluff", $response->getContent());
        $this->assertContains('"eid":1', $response->getContent());
        $this->assertContains('"name":"Test"', $response->getContent());

        $response = $this->controller->logAction($request, 'GenericLoggedEntity', 2);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isOk());
        $this->assertEquals(4, count(json_decode($response->getContent(), true)['data']));
        $this->assertContains("More Stuff", $response->getContent());
        $this->assertContains("MOAR Stuff", $response->getContent());
        $this->assertContains("MOAR cuz MOAR Stuff", $response->getContent());
        $this->assertContains('"eid":2', $response->getContent());
        $this->assertContains('"name":"Check"', $response->getContent());
    }

    public function testModAction()
    {
        static::loadFixtures(static::$kernel->getContainer()->get('doctrine')->getManager());
        $verifier = static::$kernel->getContainer()->get('lthrt.entity.class_verifier');
        $request  = new Request([],
            ['form' =>
                [
                    'json' => '{"name":"PutData", "fluff":"Fluff"}',
                ],
            ]
        );
        $request->setMethod('PUT');

        try {
            $response = $this->controller->modAction($request, 'Unaliased');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
            $this->assertContains('Unspecified class alias used in generic routing', $e->getMessage());
        }

        try {
            $response = $this->controller->modAction($request, 'GenericEntity');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('PUT request to unspecified resource; use POST without an id', $e->getMessage());
        }

        try {
            $response = $this->controller->modAction($request, 'Unaliased', 1);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
            $this->assertContains('Unspecified class alias used in generic routing', $e->getMessage());

            $entity = static::$kernel->getContainer()->get('doctrine')->getManager()->getRepository($verifier->verifyClass('GenericEntity'))->findOneById(1);
            $this->assertEquals("Test", $entity->name);
            $response = $this->controller->modAction($request, 'GenericEntity', 1);
            $this->assertEquals(302, $response->getStatusCode());
            $this->assertTrue($response->isRedirect());
            $this->assertEquals("PutData", $entity->name);

            $request = new Request([],
                ['form' =>
                    [
                        'json' => '{"name":"MorePutData"}',
                    ],
                ]
            );
            $request->setMethod('PUT');
        }try {
            $response = $this->controller->modAction($request, 'GenericEntity', 1);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
            $this->assertContains('PUT request with incompletely detailed entity', $e->getMessage());
        }

        $request = new Request([],
            ['form' =>
                [
                    'json' => '{"name":"PostData"}',
                ],
            ]
        );
        $request->setMethod('POST');

        try {
            $response = $this->controller->modAction($request, 'Unaliased', 1);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
            $this->assertContains('Unspecified class alias used in generic routing', $e->getMessage());
        }

        try {
            $response = $this->controller->modAction($request, 'Unaliased');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
            $this->assertContains('Unspecified class alias used in generic routing', $e->getMessage());
        }

        try {
            $response = $this->controller->modAction($request, 'GenericEntity', 1);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('POST request to specified resource; use PUT with id', $e->getMessage());
        }

        $response = $this->controller->modAction($request, 'GenericEntity');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect());

        $entity = static::$kernel->getContainer()->get('doctrine')->getManager()->getRepository($verifier->verifyClass('GenericEntity'))->findOneByName('PostData');
        $this->assertNotNull($entity);
    }
}
