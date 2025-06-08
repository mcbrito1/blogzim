# Blogzim

O Blogzim é um projeto para estudantes de DevOps que contém um blog simples, desenvolvido em PHP, que utiliza o banco mysql para gravar as informações. Para servir a aplicação, o docker será utilizado. 

## Tabela de conteúdos

- [Instalação](#installation)
- [Utilização](#uso)
- [Contribua](#contribua)
- [Licenciamento](#licenciamento)

## Installation

```bash
# Clone este repositório
git clone https://github.com/mcbrito1/blogzim.git

# Navegue para o diretório
cd blogzim

# Instale as dependências
docker pull mysql:5.6.36
docker pull phpmyadmin/phpmyadmin
docker pull php:8.3-apache
```

## Uso

Para iniciar o uso do projeto, utilize o comando

```bash
docker-compose up
```

## Contribua

Contribuições são bem vindas! Por favor, abra uma issue ou submeta um pull request.

## Licenciamento

Este projeto é licenciado sob a Apache License 2.0 (LICENSE).