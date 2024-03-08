[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=PicPay_fraud-check-commons&metric=alert_status&token=f6d7fc42d16a8b297658047ea8928ccaf233504d)](https://sonarcloud.io/summary/new_code?id=PicPay_fraud-check-commons)

# fraud-check-commons

Repositório compartilhado entre os micro-serviços da squad de prevenção a fraude transacional

### Code Owners
Veja nossa pagina do [Confluence](https://picpay.atlassian.net/wiki/spaces/SPF/overview)

### Como Instalar no seu Projeto
1. Configuração do vcs e instalação:
```sh 
    composer config repositories.fraud-check-commons vcs https://github.com/PicPay/fraud-check-commons \
    && composer require picpay/fraud-check-commons
```

### Contribuindo
Quando for necessário fazer alteracoes no pacote e para garantir que o projeto seja configurado completamente, execute o comando na sua maquina:
```sh
make install
```
> Vale ter em mente que, o `make install` já vai criar a imagem, instalar dependências; Restando apenas o start do serviço em si.

#### Comandos Basicos
Comandos basicos podem ser encontrados com um `make help` no terminal

#### Testes em ambiente local:

Após implementar o seu codigo basta seguir o passo a passo abaixo para fazer testes em ambiente local, usando algum projeto como cobaia(Ex: `ms-fraud-check-transaction`).

1. Subir uma branch do seu codido para o  repositorio `https://github.com/PicPay/fraud-check-commons`
2. Fazer o build do seu projeto cobaia normalmente (`docker-compose build` ou `make install`) após a imagem estar criada continue com os passos a baixo.
3. Configure o repositories do `composer.json` do seu projeto cobaia
```sh 
    composer config repositories.fraud-check-commons vcs https://github.com/PicPay/fraud-check-commons
```
4. Adicionar o pacote no campo `require` do `composer.json` com o nome da sua branch no seu projeto cobaia
   ```
   "picpay/fraud-check-commons": "dev-SUA_BRANCH",
   ```
   
5. Subir o seu projeto cobaia `docker-compose up -d` ou `make up`
6. Abrir o terminal do container `docker exec -it NOME-DO-CONTAINER sh` e executar um `composer require picpay/fraud-check-commons`