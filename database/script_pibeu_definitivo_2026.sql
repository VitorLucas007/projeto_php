DROP DATABASE IF EXISTS projeto_pibeu;
CREATE DATABASE IF NOT EXISTS projeto_pibeu;
USE projeto_pibeu;

-- TABELA: UNIDADE
-- (As filiais/polos devem ser criadas antes dos usuários, pois todos precisam se vincular a uma)
CREATE TABLE unidade (
    id_unidade INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(14) UNIQUE,
    endereco VARCHAR(255),
    telefone VARCHAR(20),
    status BOOLEAN DEFAULT TRUE
);

CREATE INDEX idx_unidade_cnpj ON unidade(cnpj);

-- TABELA: PERFIL
-- (Guarda os níveis de acesso estáticos: ADMIN, PROFESSOR, PESSOA)
CREATE TABLE perfil (
    id_perfil INT AUTO_INCREMENT PRIMARY KEY,
    nome_perfil VARCHAR(50) NOT NULL UNIQUE
);

-- Carga inicial obrigatória de perfis
INSERT INTO perfil (id_perfil, nome_perfil) VALUES 
(1, 'ADMIN'), 
(2, 'PROFESSOR'), 
(3, 'PESSOA'),
(4, 'ROOT'); -- superusuário: aprova/recusa as unidades e admins cadastrados

-- TABELA: PESSOA
-- (Centraliza os dados cadastrais básicos e anamnese de qualquer indivíduo físico)
CREATE TABLE pessoa (
    id_pessoa INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    sexo ENUM('M', 'F', 'OUTRO') NOT NULL,
    data_nascimento DATE NOT NULL,
    profissao VARCHAR(100),
    contato VARCHAR(20),
    email VARCHAR(150) UNIQUE,
    estilo_vida VARCHAR(50),
    atividade_fisica VARCHAR(150),
    tabagismo BOOLEAN,
    alcool BOOLEAN,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABELA: USUARIO
-- (Gerencia as credenciais de login, o perfil de acesso e o vínculo obrigatório com a Unidade)
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    fk_pessoa INT NOT NULL UNIQUE,
    fk_perfil INT NOT NULL,
    fk_unidade INT NOT NULL,

    login VARCHAR(100) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,

    -- Padrão 'PENDENTE'. Na aplicação, o Admin força 'APROVADO' para si mesmo no cadastro inicial
    status_aprovacao ENUM('PENDENTE', 'APROVADO', 'REJEITADO') DEFAULT 'PENDENTE',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuario_pessoa FOREIGN KEY (fk_pessoa) REFERENCES pessoa(id_pessoa) ON DELETE CASCADE,
    CONSTRAINT fk_usuario_perfil FOREIGN KEY (fk_perfil) REFERENCES perfil(id_perfil),
    CONSTRAINT fk_usuario_unidade FOREIGN KEY (fk_unidade) REFERENCES unidade(id_unidade)
);

-- TABELA: PROFESSOR
-- (Guarda informações específicas complementares ao usuário do tipo Professor)
CREATE TABLE professor (
    id_professor INT AUTO_INCREMENT PRIMARY KEY,
    fk_pessoa INT NOT NULL UNIQUE,
    cref VARCHAR(30),
    especialidade VARCHAR(100),
    
    CONSTRAINT fk_professor_pessoa FOREIGN KEY (fk_pessoa) REFERENCES pessoa(id_pessoa) ON DELETE CASCADE
);

-- TABELA: ALUNO
-- (Guarda informações específicas complementares ao usuário do tipo Aluno/Pessoa)
CREATE TABLE aluno (
    id_aluno INT AUTO_INCREMENT PRIMARY KEY,
    fk_pessoa INT NOT NULL UNIQUE,
    observacoes TEXT,
    
    CONSTRAINT fk_aluno_pessoa FOREIGN KEY (fk_pessoa) REFERENCES pessoa(id_pessoa) ON DELETE CASCADE
);

-- TABELA: PRONTUARIO
CREATE TABLE prontuario (
    id_prontuario INT AUTO_INCREMENT PRIMARY KEY,
    fk_aluno INT NOT NULL UNIQUE,
    data_abertura DATE NOT NULL,
    observacoes_gerais TEXT,
    
    CONSTRAINT fk_prontuario_aluno FOREIGN KEY (fk_aluno) REFERENCES aluno(id_aluno) ON DELETE CASCADE
);

-- TABELA: AVALIACAO
CREATE TABLE avaliacao (
    id_avaliacao INT AUTO_INCREMENT PRIMARY KEY,
    fk_prontuario INT NOT NULL,
    fk_professor INT NOT NULL,
    data_avaliacao DATE NOT NULL,
    
    frequencia_cardiaca VARCHAR(20),
    pressao_arterial VARCHAR(20),
    sedentario BOOLEAN,
    atividade_fisica VARCHAR(150),
    tabagismo BOOLEAN,
    alcool BOOLEAN,
    
    medicacao_controlada BOOLEAN,
    medicamentos_descricao TEXT,
    problema_osteoarticular BOOLEAN,
    osteoarticular_descricao TEXT,
    problema_neuromuscular BOOLEAN,
    neuromuscular_descricao TEXT,
    problema_coronario BOOLEAN,
    coronario_descricao TEXT,
    problema_vascular BOOLEAN,
    hospitalizacao_5_anos BOOLEAN,
    hospitalizacao_descricao TEXT,
    cirurgia_5_anos BOOLEAN,
    cirurgia_descricao TEXT,
    
    torax DECIMAL(5,2),
    cintura DECIMAL(5,2),
    abdominal DECIMAL(5,2),
    quadril DECIMAL(5,2),
    braco_relaxado_direito DECIMAL(5,2),
    braco_relaxado_esquerdo DECIMAL(5,2),
    braco_contraido_direito DECIMAL(5,2),
    braco_contraido_esquerdo DECIMAL(5,2),
    coxa_direita DECIMAL(5,2),
    coxa_esquerda DECIMAL(5,2),
    panturrilha_direita DECIMAL(5,2),
    panturrilha_esquerda DECIMAL(5,2),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_avaliacao_prontuario FOREIGN KEY (fk_prontuario) REFERENCES prontuario(id_prontuario),
    CONSTRAINT fk_avaliacao_professor FOREIGN KEY (fk_professor) REFERENCES professor(id_professor)
);

-- TABELA: LAUDO
CREATE TABLE laudo (
    id_laudo INT AUTO_INCREMENT PRIMARY KEY,
    fk_avaliacao INT NOT NULL UNIQUE,
    descricao TEXT NOT NULL,
    data_emissao DATE NOT NULL,
    
    CONSTRAINT fk_laudo_avaliacao FOREIGN KEY (fk_avaliacao) REFERENCES avaliacao(id_avaliacao) ON DELETE CASCADE
);

-- TABELA: PRESCRICAO
CREATE TABLE prescricao (
    id_prescricao INT AUTO_INCREMENT PRIMARY KEY,
    fk_laudo INT NOT NULL,
    descricao TEXT NOT NULL,
    data_prescricao DATE NOT NULL,
    
    CONSTRAINT fk_prescricao_laudo FOREIGN KEY (fk_laudo) REFERENCES laudo(id_laudo) ON DELETE CASCADE
);

-- TABELA: MENSAGENS
CREATE TABLE mensagens (
    id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
    fk_emissor INT NOT NULL,
    fk_receptor INT NOT NULL,
    conteudo TEXT NOT NULL,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_mensagem_emissor FOREIGN KEY (fk_emissor) REFERENCES usuario(id_usuario),
    CONSTRAINT fk_mensagem_receptor FOREIGN KEY (fk_receptor) REFERENCES usuario(id_usuario)
);

-- ==========================================================================
-- MOCK: USUÁRIO ROOT (superusuário)
-- Fica acima do Admin: aprova ou recusa as unidades/admins cadastrados.
-- Login: RootUser | Senha: root123
-- Hash abaixo gerado com password_hash('root123', PASSWORD_DEFAULT)
-- ==========================================================================

-- Unidade "de sistema" só para satisfazer o vínculo obrigatório fk_unidade do Root
INSERT INTO unidade (nome, cnpj, endereco, telefone, status)
VALUES ('Unidade Sistema (Root)', NULL, NULL, NULL, TRUE);

INSERT INTO pessoa (nome, sexo, data_nascimento, profissao, contato, email, estilo_vida, atividade_fisica, tabagismo, alcool)
VALUES ('Root', 'OUTRO', '2000-01-01', 'Superusuário', NULL, 'RootUser', NULL, NULL, 0, 0);

INSERT INTO usuario (fk_pessoa, fk_perfil, fk_unidade, login, senha_hash, status_aprovacao, ativo)
VALUES (
    (SELECT id_pessoa FROM pessoa WHERE email = 'RootUser'),
    4, -- ROOT
    (SELECT id_unidade FROM unidade WHERE nome = 'Unidade Sistema (Root)'),
    'RootUser',
    '$2b$10$//MnlyAuohvNYeFPSknYXuTakqmUE2O6MQ9OhWOd0CR1yfJlGaydm', -- root123
    'APROVADO',
    1
);
