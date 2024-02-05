# Elementor Forms Cademí
___

Plugin da Cademí desenvolvido para adicionar ações de pós envio de formulários nativos do Elementor PRO

___
### Como instalar?

Nossa versão beta ainda não foi disponibilizada no repositório oficial do Wordpress, para instalar siga os passos:

1. Baixe a última versão através [desse link](https://github.com/minhacademi/elementor-forms-cademi/archive/refs/heads/master.zip).
2. Acesse a área de plugins do seu Wordpress
   * Adicionar novo;
   * Fazer upload;
   * Selecione o .zip que você baixou;
3. Após instalar, a ação "Redirect - Cademí" irá aparecer nas opções de configuração dos formulários dentro do Elementor.

___

### Como usar?
Após selecionar a ação, uma caixa irá surgir na barra lateral com o título "Redirect - Cademí" com os campos:

| Campo               | Obrigatório | Descrição                                                                                            | 
|---------------------|-------------|------------------------------------------------------------------------------------------------------|
| URL da plataforma   | Sim         | Você pode encontrar a URL da sua plataforma em: Configurações > Domínio padrão                       | 
| Token da plataforma | Sim         | Você pode encontrar o Token da sua plataforma em: Configurações > Token da Plataforma                | 
| ID da entrega       | -           | Ao definir uma ID você irá adicionará a Entrega para o usuário, além da entrega gratuita configurada |
| Redirect interno    | -           | Ao configurar um Redirect Interno, o usuário será redirecionado para esta URL após o login.          |
| ID do campo Nome    | -           | ID do campo Nome do aluno, você encontra esse ID ao editar o campo na aba "Avançado"                 |
| ID do campo Email   | -           | ID do campo Email do aluno, você encontra esse ID ao editar o campo na aba "Avançado"                |
| ID do campo Celular | -           | ID do campo Celular do aluno, você encontra esse ID ao editar o campo na aba "Avançado"              |

> Lembre-se de manter essa ação como a última da sua lista de ações pois ela irá redirecionar o usuário para a Cademí