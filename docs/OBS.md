# Informações adicionais

Algumas coisas especificas da versão **0.0.0-a**.

## Para adicionar cartões

Existe um script chamado ``addCard.php`` dentro do diretório ``app/bin/addCard.php``

A forma de usar por padrão é executar ele e passar um parâmetro na linha de comando.

Supondo que no diretório anterior a pasta ``app/`` exista um arquivo chamado ``test.txt`` e lá contém seus cartões, pra adicionar no bot seria algo parecido com:

```bash
php app/bin/addCard.php test.txt
```

Pra saber se o arquivo está no mesmo diretório que você basta dar o comando ``ls``, deverá ver algo como:

```bash
fernando@fernando-B450MX-S:~/bots/test/bot_ccs$ ls
app  counter.php  test.txt  OBS.md  README.md
fernando@fernando-B450MX-S:~/bots/test/bot_ccs$ 
```

Se seu caso está igual a esse exemplo basta executar o script:

```bash
php app/bin/addCard.php
```

E após ``addCard.php`` passar o nome do arquivo como parâmetro, exemplo:

```bash
php app/bin/addCard.php test.txt
```

Espero que tenha ajudado :)