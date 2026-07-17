Como rodar o projeto localmente:

 1. Preparando os Arquivos
1. Mova a pasta do seu projeto para dentro da pasta padrão de servidores do XAMPP:
   * **No Windows:** `C:\xampp\htdocs\restaurante_chefinhas`
   * **No macOS:** `/Applications/XAMPP/xamppfiles/htdocs/restaurante_chefinhas`

 2. Iniciando o XAMPP
1. Abra o **XAMPP Control Panel**.
2. Clique em **Start** no **Apache** (porta padrão: `80`).
3. Clique em **Start** no **MySQL** (porta padrão: `3306`).

3. Configurando o Banco de Dados (MySQL)
1. Acesse o painel do banco no navegador: [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)
2. Clique em **"Novo"** no menu esquerdo.
3. Defina o nome exatamente como: `restaurante_chefinhas` e clique em **Criar**.
4. Acesse a aba **Importar** no topo, selecione o arquivo de backup `.sql` do seu projeto e clique em **Executar** (no final da página).

4. Credenciais de Conexão no PHP
Certifique-se de que o seu arquivo de conexão local (`conexao.php` ou equivalente) está configurado com os valores padrão do XAMPP:
* **Host:** `localhost` (ou `127.0.0.1`)
* **Nome do Banco:** `restaurante_chefinhas`
* **Usuário:** `root`
* **Senha:** *(vazio/em branco)*

5. Acessando o Sistema
* **Painel do Administrador:** [http://localhost/restaurante_chefinhas/admin/admin.php](http://localhost/restaurante_chefinhas/admin/admin.php)
* **Página do Cardápio/Cliente:** [http://localhost/restaurante_chefinhas/](http://localhost/restaurante_chefinhas/)
