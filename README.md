# O Começo

Dessa vez eu quis trazer algo inovador pra esses bots.
E por isso **você terá que ter acesso sudo (root) na sua máquina**.

Recomendo que esteja no linux, se estiver no windows, compre uma vps com ubuntu para rodar tudo.

Uma opção é rodar um subsitema **Ubuntu** no Windows diretamente.

## Aviso

Versão alpha, não espere fazer vendas ou quaisquer outro tipo de coisa nessa versão. **0.0.0-a**

### Requesitos

- Php
- Composer
- SQLite

Antes de subir tudo, vamos testar em local mesmo. Certifique-se que todos os requesitos estejam instalados, ou instale-os com os comandos abaixo:
**(Certifique-se que esteja no diretório correto -> app/)**

```bash
apt install -y composer php
composer run fernando:install
composer install
```

Verifique a versão do php com o comando:

```bash
php -v
```

Verá algo parecido com:

```bash
fernando@fernando-B450MX-S:~/bots/bot_ccs$ php -v
PHP 8.3.8 (cli) (built: Jun  8 2024 21:34:22) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.8, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.8, Copyright (c), by Zend Technologies
fernando@fernando-B450MX-S:~/bots/bot_ccs$ 
```

Se já estiver na **8.3** ou superior continuemos, caso contrário, procure como adicionar a versão mais recente do php em sua máquina.

### Configure o banco de dados e o bot

Execute o comando:

```bash
composer run fernando:configure
```

Ele irá configurar o banco de dados pra você.

### ENV

Todos os dados do bot (os mais sensíveis) estão localizados em ``app/environment/.env``, existe um ``.env.example`` que serve apenas para demonstrar como deve ser configurado, o bot usa apenas o ``.env`` para carregar os dados.

Por fim rode:

```bash
composer run fernando:bot --timeout 0
```

Se retornar `Zanzara is Listening` é porque seu bot já está online.
