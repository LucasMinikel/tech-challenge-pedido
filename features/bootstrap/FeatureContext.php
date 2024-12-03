<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;

class FeatureContext implements Context
{
    private $client;
    private $response;
    private $pedidoData;
    private $mockHandler;

    public function __construct()
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->client = new Client(['handler' => $handlerStack]);
    }

    /**
     * @Given eu tenho os seguintes dados do pedido:
     */
    public function euTenhoOsSeguintesDadosDoPedido(TableNode $table)
    {
        $pedido = $table->getRowsHash();
        $pedido['itens'] = json_decode($pedido['itens'], true);
        $this->pedidoData = json_encode($pedido);
    }

    /**
     * @When eu envio uma requisição POST para :path com os dados do pedido
     */
    public function euEnvioUmaRequisicaoPostParaComOsDadosDoPedido($path)
    {
        // Aqui definimos a resposta mockada
        $this->mockHandler->append(new Response(201, [], json_encode([
            'message' => 'Pedido criado com sucesso',
            'id' => '12345'
        ])));

        $this->response = $this->client->post($path, [
            'body' => $this->pedidoData,
            'headers' => ['Content-Type' => 'application/json']
        ]);
    }

    /**
     * @Then o código de status da resposta deve ser :code
     */
    public function oCodigoDeStatusDaRespostaDeveSer($code)
    {
        Assert::assertEquals($code, $this->response->getStatusCode());
    }

    /**
     * @Then a resposta deve conter :text
     */
    public function aRespostaDeveConter($text)
    {
        Assert::assertStringContainsString($text, (string) $this->response->getBody());
    }

    /**
     * @Then a resposta deve conter um :field do pedido
     */
    public function aRespostaDeveConterUmDoPedido($field)
    {
        $responseData = json_decode((string) $this->response->getBody(), true);
        Assert::assertArrayHasKey($field, $responseData);
        Assert::assertNotEmpty($responseData[$field]);
    }
}
