Feature: Criar Pedido
  Como um cliente
  Eu quero criar um novo pedido
  Para que eu possa registrar minha compra

  Scenario: Criar um pedido válido
    Given eu tenho os seguintes dados do pedido:
      | clienteId | 123 |
      | itens     | [{"produtoId": "456", "quantidade": 2}, {"produtoId": "789", "quantidade": 1}] |
    When eu envio uma requisição POST para "/pedidos" com os dados do pedido
    Then o código de status da resposta deve ser 201
    And a resposta deve conter "Pedido criado com sucesso"
    And a resposta deve conter um "id" do pedido