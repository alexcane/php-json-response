<?php


use PhpJsonResp\JsonResp;
use PHPUnit\Framework\TestCase;

class JsonRespTest extends TestCase
{
    private JsonResp $resp;

    protected function setUp(): void
    {
        $this->resp = new JsonResp();
    }

    public function testConstructorWithEmptyData()
    {
        $resp = new JsonResp();
        $this->assertSame([], $resp->getData());
    }

    public function testConstructorWithData()
    {
        $resp = new JsonResp(['name' => 'John', 'age' => 30]);
        $this->assertSame(['name' => 'John', 'age' => 30], $resp->getData());
    }

    public function testSetDataTrimStrings()
    {
        $this->resp->setData(['name' => '  John  ', 'city' => 'Paris   ']);
        $data = $this->resp->getData();

        $this->assertSame('John', $data['name']);
        $this->assertSame('Paris', $data['city']);
    }

    public function testSetDataConvertTrueString()
    {
        $this->resp->setData(['active' => 'true', 'verified' => 'false']);
        $data = $this->resp->getData();

        $this->assertTrue($data['active']);
        $this->assertFalse($data['verified']);
    }

    public function testSetDataPreservesNonStringTypes()
    {
        $this->resp->setData(['count' => 42, 'price' => 19.99, 'items' => ['a', 'b']]);
        $data = $this->resp->getData();

        $this->assertSame(42, $data['count']);
        $this->assertSame(19.99, $data['price']);
        $this->assertSame(['a', 'b'], $data['items']);
    }

    public function testGetData()
    {
        $this->resp->setData(['key' => 'value']);
        $this->assertSame(['key' => 'value'], $this->resp->getData());
    }

    public function testClearData()
    {
        $this->resp->setData(['key' => 'value']);
        $this->resp->clearData();
        $this->assertSame([], $this->resp->getData());
    }

    public function testAddErrMsg()
    {
        $this->resp->addErrMsg('Error 1');
        $this->resp->addErrMsg('Error 2');

        $errors = $this->resp->getErrMsg();
        $this->assertCount(2, $errors);
        $this->assertSame('Error 1', $errors[0]);
        $this->assertSame('Error 2', $errors[1]);
    }

    public function testSetErrMsg()
    {
        $this->resp->addErrMsg('Initial error');
        $this->resp->setErrMsg(['New error 1', 'New error 2']);

        $errors = $this->resp->getErrMsg();
        $this->assertCount(2, $errors);
        $this->assertSame('New error 1', $errors[0]);
        $this->assertSame('New error 2', $errors[1]);
    }

    public function testGetErrMsg()
    {
        $this->assertSame([], $this->resp->getErrMsg());

        $this->resp->addErrMsg('Test error');
        $this->assertSame(['Test error'], $this->resp->getErrMsg());
    }

    public function testErrorCount()
    {
        $this->assertSame(0, $this->resp->errorCount());

        $this->resp->addErrMsg('Error 1');
        $this->assertSame(1, $this->resp->errorCount());

        $this->resp->addErrMsg('Error 2');
        $this->assertSame(2, $this->resp->errorCount());
    }

    public function testIsSuccessWithNoErrors()
    {
        $this->assertTrue($this->resp->isSuccess());
    }

    public function testIsSuccessWithErrors()
    {
        $this->resp->addErrMsg('Error');
        $this->assertFalse($this->resp->isSuccess());
    }

    public function testIsErrorWithNoErrors()
    {
        $this->assertFalse($this->resp->isError());
    }

    public function testIsErrorWithErrors()
    {
        $this->resp->addErrMsg('Error');
        $this->assertTrue($this->resp->isError());
    }

    public function testGetResponse()
    {
        $this->assertNull($this->resp->getResponse());
    }

    public function testSetResponseWithString()
    {
        $this->resp->setResponse('Test response');
        $this->assertSame('Test response', $this->resp->getResponse());
    }

    public function testSetResponseWithArray()
    {
        $this->resp->setResponse(['key' => 'value']);
        $this->assertSame(['key' => 'value'], $this->resp->getResponse());
    }

    public function testSetResponseWithObject()
    {
        $obj = new stdClass();
        $obj->name = 'Test';
        $this->resp->setResponse($obj);
        $this->assertSame($obj, $this->resp->getResponse());
    }

    public function testReturnResponseAsArrayWithSuccess()
    {
        $this->resp->setData(['user' => 'John']);
        $response = $this->resp->returnResponse();

        $this->assertIsArray($response);
        $this->assertSame('success', $response['status']);
        $this->assertSame([], $response['error_msg']);
        $this->assertSame(['user' => 'John'], $response['data']);
        $this->assertArrayNotHasKey('response', $response);
    }

    public function testReturnResponseAsArrayWithError()
    {
        $this->resp->addErrMsg('Invalid input');
        $this->resp->setData(['user' => 'John']);
        $response = $this->resp->returnResponse();

        $this->assertIsArray($response);
        $this->assertSame('error', $response['status']);
        $this->assertSame(['Invalid input'], $response['error_msg']);
        $this->assertSame(['user' => 'John'], $response['data']);
    }

    public function testReturnResponseOmitsEmptyData()
    {
        $this->resp->addErrMsg('Error');
        $response = $this->resp->returnResponse();

        $this->assertArrayNotHasKey('data', $response);
        $this->assertArrayNotHasKey('response', $response);
    }

    public function testReturnResponseIncludesResponseField()
    {
        $this->resp->setResponse('Custom response');
        $response = $this->resp->returnResponse();

        $this->assertSame('Custom response', $response['response']);
    }

    public function testReturnResponseAsJson()
    {
        $this->resp->setData(['user' => 'John']);
        $json = $this->resp->returnResponse(true);

        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertSame('success', $decoded['status']);
        $this->assertSame(['user' => 'John'], $decoded['data']);
    }

    public function testReturnResponseAsJsonWithError()
    {
        $this->resp->addErrMsg('Error 1');
        $this->resp->addErrMsg('Error 2');
        $json = $this->resp->returnResponse(true);

        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertSame('error', $decoded['status']);
        $this->assertSame(['Error 1', 'Error 2'], $decoded['error_msg']);
    }

    public function testReturnResponseJsonThrowsOnInvalidData()
    {
        $this->expectException(JsonException::class);

        // Create invalid UTF-8 sequence that will fail json_encode
        $this->resp->setData(['invalid' => "\xB1\x31"]);
        $this->resp->returnResponse(true);
    }

    public function testCompleteWorkflow()
    {
        // Simulate a complete validation workflow
        $resp = new JsonResp(['email' => '  test@example.com  ', 'active' => 'true']);

        // Verify data processing
        $data = $resp->getData();
        $this->assertSame('test@example.com', $data['email']);
        $this->assertTrue($data['active']);

        // Add validation errors
        $resp->addErrMsg('Email already exists');

        // Verify error state
        $this->assertTrue($resp->isError());
        $this->assertFalse($resp->isSuccess());
        $this->assertSame(1, $resp->errorCount());

        // Get response
        $response = $resp->returnResponse();
        $this->assertSame('error', $response['status']);
        $this->assertSame(['Email already exists'], $response['error_msg']);
        $this->assertSame(['email' => 'test@example.com', 'active' => true], $response['data']);
    }

    public function testMultipleDataSets()
    {
        $this->resp->setData(['first' => 'value1']);
        $this->assertSame(['first' => 'value1'], $this->resp->getData());

        $this->resp->setData(['second' => 'value2']);
        $this->assertSame(['second' => 'value2'], $this->resp->getData());
    }

    public function testMixedStringBooleanConversion()
    {
        $this->resp->setData([
            'bool_true' => 'true',
            'bool_false' => 'false',
            'string_true' => 'TRUE',
            'string_false' => 'FALSE',
            'actual_bool' => true
        ]);

        $data = $this->resp->getData();
        $this->assertTrue($data['bool_true']);
        $this->assertFalse($data['bool_false']);
        $this->assertSame('TRUE', $data['string_true']); // Only 'true' lowercase is converted
        $this->assertSame('FALSE', $data['string_false']); // Only 'false' lowercase is converted
        $this->assertTrue($data['actual_bool']);
    }
}
