<?php
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Skill5 Moodle';
$string['overview'] = 'Visão Geral';
$string['failedcreateltool'] = 'Falha ao Criar Ferramenta LTI';
$string['connectionfailed'] = 'Falha na Conexão com a API Skill5';
$string['admin_email'] = 'Email do Administrador Skill5';
$string['admin_email_desc'] = 'Digite o email associado à sua conta de administrador Skill5.';
$string['entityuserid_from_email'] = 'ID de Usuário da Entidade Skill5';
$string['entityuserid_from_email_desc'] = 'Isso será buscado automaticamente após a conexão.';
$string['connect_button'] = 'Conectar com Skill5';
$string['connection_failed'] = 'Falha na conexão com Skill5. Por favor, verifique suas configurações e tente novamente. Detalhes do erro: {$a}';
$string['connect_heading'] = 'Conexão Automática';

// LTI Management Page
$string['ltimanagement'] = 'Gerenciamento LTI';
$string['lticonnected'] = 'A ferramenta LTI Skill5 está conectada e ativa.';
$string['ltinotconnected'] = 'A ferramenta LTI Skill5 não está conectada. Por favor, vá para a página de configurações principal para conectar.';
$string['connect'] = 'Ir para a página de conexão';

// LTI Management Page - Revamped
$string['connectiondetails'] = 'Detalhes da Conexão';
$string['ltitoolinfo'] = 'Informações da Ferramenta LTI';
$string['skill5userinfo'] = 'Informações do Usuário Skill5';
$string['label_clientid'] = 'ID do Cliente';
$string['label_adminname'] = 'Nome do Administrador';
$string['label_adminemail'] = 'Email do Administrador';
$string['label_entityuserid'] = 'ID de Usuário da Entidade Skill5';
$string['nextsteps'] = 'Próximos Passos';
$string['step1_heading'] = 'Passo 1: Habilitar a Ferramenta';
$string['step1_text'] = 'A ferramenta LTI Skill5 foi criada, mas está desabilitada por padrão. Você precisa habilitá-la para disponibilizá-la aos professores nos cursos.';
$string['step1_instruction'] = 'Vá para {$a} e clique no ícone de \'olho\' para habilitar a \'Ferramenta LTI Skill5\'.';
$string['managetools_link_text'] = 'Gerenciar ferramentas';
$string['step2_heading'] = 'Passo 2: Adicionar a Ferramenta a um Curso';
$string['step2_text'] = 'Uma vez habilitada, você ou seus professores podem adicionar a ferramenta Skill5 a qualquer curso.';
$string['step2_instruction_1'] = 'Navegue até um curso e ative o \'Modo de edição\'.';
$string['step2_instruction_2'] = 'Clique em \'Adicionar uma atividade ou recurso\' e selecione \'Ferramenta LTI Skill5\' da lista.';
$string['step2_instruction_3'] = 'Clique no botão \'Selecionar conteúdo\'. Isso abrirá a biblioteca de conteúdo Skill5, permitindo que você escolha o curso que deseja vincular.';
$string['step2_instruction_4'] = 'Salve a atividade. Os alunos agora podem acessar o conteúdo Skill5 diretamente do curso Moodle.';

// Settings Page - Revamped
$string['settings_intro_heading'] = 'Conecte seu Moodle ao Skill5';
$string['settings_intro_text'] = 'Digite seu email de administrador Skill5 abaixo e clique no botão conectar. Isso criará e configurará automaticamente a ferramenta LTI 1.3 para você.';
$string['connection_established_heading'] = 'Conexão Estabelecida';
$string['connection_established_text'] = 'Uma conexão Skill5 já está configurada para este site.';
$string['connection_established_tip'] = 'Você pode visualizar os detalhes da conexão na {$a}. Se precisar gerar uma nova conexão, você deve primeiro excluir a conexão existente da página de ferramentas LTI do Moodle.';
$string['ltimanagement_link_text'] = 'página de Gerenciamento LTI';

// User Management Page
$string['usermanagement'] = 'Gerenciamento de Usuários';

// Connection Assistant Page & Summary
$string['connectionassistant'] = 'Assistente de Conexão Skill5';
$string['connectionstatus'] = 'Status da Conexão';
$string['summary_connected'] = 'A conexão com Skill5 está ativa para o usuário: {$a}.';
$string['summary_connected_tip'] = 'Para gerenciar a conexão, vá para {$a}.';
$string['summary_not_connected'] = 'A conexão com Skill5 não está configurada.';
$string['summary_not_connected_tip'] = 'Para começar, vá para {$a}.';

// Privacy API.
$string['privacy:metadata:skill5_lti'] = 'Para integrar com a plataforma Skill5, os dados do usuário são trocados com o serviço LTI externo Skill5.';
$string['privacy:metadata:skill5_lti:userid'] = 'O ID do usuário é enviado do Moodle para permitir que você acesse seus dados na plataforma Skill5.';
$string['privacy:metadata:skill5_lti:fullname'] = 'Seu nome completo é enviado para a plataforma Skill5 para fornecer uma experiência de aprendizado personalizada.';
$string['privacy:metadata:skill5_lti:email'] = 'Seu endereço de email é enviado para a plataforma Skill5 para permitir a identificação da conta e comunicação.';

// Error messages.
$string['error_api_jwt_secret'] = 'Segredo JWT da API não encontrado na configuração. Por favor, reconecte o plugin.';
$string['error_entity_user_id'] = 'ID de Usuário da Entidade do Administrador não encontrado na configuração. Por favor, reconecte o plugin.';
$string['error_invalid_response'] = 'Resposta inválida da API Skill5 ao buscar o ID de Usuário da Entidade.';
$string['error_curl_request'] = 'Requisição cURL falhou com erro: {$a}';
$string['error_api_request'] = 'Requisição da API para {$a->endpoint} falhou com código HTTP {$a->httpcode}. Resposta: {$a->response}';
$string['error_missing_admin_email'] = 'Email do administrador não configurado. Por favor, configure o email do administrador nas configurações.';
$string['error_fetch_entity_data'] = 'Não foi possível buscar dados da Entidade da API Skill5. Resposta: {$a}';
$string['error_missing_entity_fields'] = 'Resposta inválida da API Skill5. Faltando entityUserId, entityId ou jwtSecret.';
$string['error_lti_no_id'] = 'lti_add_type não retornou um ID válido.';
$string['error_lti_creation_failed'] = 'Falha ao criar ferramenta LTI: {$a}';
$string['error_unknown_lti_server'] = 'Erro desconhecido do Servidor LTI.';
$string['error_register_lti_platform'] = 'Falha ao registrar plataforma no Servidor LTI (HTTP {$a->httpcode}): {$a->message}';
$string['error_register_skill5_app'] = 'Falha ao registrar Moodle no App Skill5. Resposta: {$a}';
$string['error_unexpected'] = 'Ocorreu um erro inesperado';
$string['error_fetch_user_details'] = 'Erro ao buscar detalhes do usuário do Skill5';
$string['error_user_not_found'] = 'Usuário não encontrado.';
$string['error_fetch_users'] = 'Erro ao buscar usuários do Skill5';
$string['error_connection_failed'] = 'Falha na conexão. Por favor, tente novamente ou entre em contato com o suporte.';

// LTI Tool.
$string['lti_tool_name'] = 'Ferramenta LTI Skill5';
$string['lti_tool_description'] = 'Ferramenta LTI para integração com a plataforma Skill5.';

// User details page.
$string['user_details_heading'] = 'Detalhes do Usuário: {$a}';
$string['course_progress'] = 'Progresso do Curso';
$string['course'] = 'Curso';
$string['completed_at'] = 'Concluído Em';
$string['not_completed'] = '-';
$string['no_course_progress'] = 'Nenhum progresso de curso encontrado.';
$string['badges'] = 'Emblemas';
$string['badge'] = 'Emblema';
$string['issued_at'] = 'Emitido Em';
$string['no_badges'] = 'Nenhum emblema encontrado.';
$string['certificates'] = 'Certificados';
$string['certificate'] = 'Certificado';
$string['no_certificates'] = 'Nenhum certificado encontrado.';

// User management page.
$string['login_count'] = 'Contagem de Login';
$string['last_login'] = 'Último Login';
$string['never'] = 'Nunca';
$string['view_details'] = 'Ver Detalhes';
