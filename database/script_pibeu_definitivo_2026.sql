DROP DATABASE IF EXISTS projeto_pibeu;
CREATE DATABASE IF NOT EXISTS projeto_pibeu;
USE projeto_pibeu;


CREATE TABLE unidade (
    id_unidade INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(14) UNIQUE,
    endereco VARCHAR(255),
    telefone VARCHAR(20),
    status BOOLEAN DEFAULT TRUE
);

CREATE INDEX idx_unidade_cnpj ON unidade(cnpj);


CREATE TABLE perfil (
    id_perfil INT AUTO_INCREMENT PRIMARY KEY,
    nome_perfil VARCHAR(50) NOT NULL UNIQUE
);


INSERT INTO perfil (id_perfil, nome_perfil) VALUES 
(1, 'ADMIN'), 
(2, 'PROFESSOR'), 
(3, 'PESSOA'),
(4, 'ROOT'); -- superusuário: mock no banco.


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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cpf VARCHAR(11) UNIQUE
);


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


CREATE TABLE professor (
    id_professor INT AUTO_INCREMENT PRIMARY KEY,
    fk_pessoa INT NOT NULL UNIQUE,
    cref VARCHAR(30),
    especialidade VARCHAR(100),
    
    CONSTRAINT fk_professor_pessoa FOREIGN KEY (fk_pessoa) REFERENCES pessoa(id_pessoa) ON DELETE CASCADE
);


CREATE TABLE aluno (
    id_aluno INT AUTO_INCREMENT PRIMARY KEY,
    fk_pessoa INT NOT NULL UNIQUE,
    observacoes TEXT,
    
    CONSTRAINT fk_aluno_pessoa FOREIGN KEY (fk_pessoa) REFERENCES pessoa(id_pessoa) ON DELETE CASCADE
);


CREATE TABLE prontuario (
    id_prontuario INT AUTO_INCREMENT PRIMARY KEY,
    fk_aluno INT NOT NULL UNIQUE,
    data_abertura DATE NOT NULL,
    observacoes_gerais TEXT,
    
    CONSTRAINT fk_prontuario_aluno FOREIGN KEY (fk_aluno) REFERENCES aluno(id_aluno) ON DELETE CASCADE
);


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

    peso DECIMAL(5,2),
    percentual_gordura DECIMAL(5,2),
    massa_magra DECIMAL(5,2),
    massa_muscular DECIMAL(5,2),
    agua_corporal DECIMAL(5,2),
    imc DECIMAL(5,2),
    taxa_metabolica_basal DECIMAL(6,2),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_avaliacao_prontuario FOREIGN KEY (fk_prontuario) REFERENCES prontuario(id_prontuario),
    CONSTRAINT fk_avaliacao_professor FOREIGN KEY (fk_professor) REFERENCES professor(id_professor)
);

-- TABELA: FICHA_COMPOSICAO_CORPORAL
-- Ficha detalhada de bioimpedância (estilo InBody), preenchida pelo
-- professor e vinculada a uma avaliação física já existente — o verso da
-- impressão reaproveita a anamnese e as medidas da avaliação vinculada.
CREATE TABLE ficha_composicao_corporal (
    id_ficha INT AUTO_INCREMENT PRIMARY KEY,
    fk_avaliacao INT NOT NULL,
    fk_professor INT NOT NULL,
    data_teste DATE NOT NULL,
    hora_teste TIME,
    id_equipamento VARCHAR(50),

    altura DECIMAL(5,2),
    sexo_referencia ENUM('M', 'F'),
    idade_referencia INT,

    agua_corporal_total DECIMAL(5,2),
    agua_corporal_min DECIMAL(5,2),
    agua_corporal_max DECIMAL(5,2),

    proteina DECIMAL(5,2),
    proteina_min DECIMAL(5,2),
    proteina_max DECIMAL(5,2),

    minerais DECIMAL(5,2),
    minerais_min DECIMAL(5,2),
    minerais_max DECIMAL(5,2),

    massa_gordura DECIMAL(5,2),
    massa_gordura_min DECIMAL(5,2),
    massa_gordura_max DECIMAL(5,2),

    peso DECIMAL(5,2),
    peso_min DECIMAL(5,2),
    peso_max DECIMAL(5,2),

    massa_muscular_esqueletica DECIMAL(5,2),
    mme_min DECIMAL(5,2),
    mme_max DECIMAL(5,2),

    imc DECIMAL(5,2),
    imc_min DECIMAL(5,2),
    imc_max DECIMAL(5,2),

    percentual_gordura_corporal DECIMAL(5,2),
    pgc_min DECIMAL(5,2),
    pgc_max DECIMAL(5,2),

    peso_ideal DECIMAL(5,2),
    controle_peso DECIMAL(5,2),
    controle_gordura DECIMAL(5,2),
    controle_muscular DECIMAL(5,2),
    pontuacao_geral INT,

    relacao_cintura_quadril DECIMAL(4,2),
    nivel_gordura_visceral INT,
    angulo_fase DECIMAL(4,2),

    massa_livre_gordura DECIMAL(5,2),
    mlg_min DECIMAL(5,2),
    mlg_max DECIMAL(5,2),

    fator_atividade DECIMAL(3,2),
    taxa_metabolica_basal DECIMAL(6,2),
    tmb_min DECIMAL(6,2),
    tmb_max DECIMAL(6,2),

    grau_obesidade DECIMAL(5,2),
    grau_obesidade_min DECIMAL(5,2),
    grau_obesidade_max DECIMAL(5,2),

    smi DECIMAL(5,2),
    ingestao_calorica_recomendada DECIMAL(6,2),

    seg_magra_braco_esq_kg DECIMAL(5,2),
    seg_magra_braco_esq_pct DECIMAL(5,2),
    seg_magra_braco_dir_kg DECIMAL(5,2),
    seg_magra_braco_dir_pct DECIMAL(5,2),
    seg_magra_tronco_kg DECIMAL(5,2),
    seg_magra_tronco_pct DECIMAL(5,2),
    seg_magra_perna_esq_kg DECIMAL(5,2),
    seg_magra_perna_esq_pct DECIMAL(5,2),
    seg_magra_perna_dir_kg DECIMAL(5,2),
    seg_magra_perna_dir_pct DECIMAL(5,2),

    seg_gordura_braco_esq_kg DECIMAL(5,2),
    seg_gordura_braco_esq_pct DECIMAL(5,2),
    seg_gordura_braco_dir_kg DECIMAL(5,2),
    seg_gordura_braco_dir_pct DECIMAL(5,2),
    seg_gordura_tronco_kg DECIMAL(5,2),
    seg_gordura_tronco_pct DECIMAL(5,2),
    seg_gordura_perna_esq_kg DECIMAL(5,2),
    seg_gordura_perna_esq_pct DECIMAL(5,2),
    seg_gordura_perna_dir_kg DECIMAL(5,2),
    seg_gordura_perna_dir_pct DECIMAL(5,2),

    observacoes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_ficha_composicao_avaliacao FOREIGN KEY (fk_avaliacao) REFERENCES avaliacao(id_avaliacao) ON DELETE CASCADE,
    CONSTRAINT fk_ficha_composicao_professor FOREIGN KEY (fk_professor) REFERENCES professor(id_professor)
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

-- TABELA: FICHA_TREINO
CREATE TABLE ficha_treino (
    id_ficha INT AUTO_INCREMENT PRIMARY KEY,
    fk_aluno INT NOT NULL,
    fk_professor INT NOT NULL,
    nome_treino VARCHAR(100) NOT NULL,
    data_criacao DATE NOT NULL,
    data_validade DATE,
    observacoes TEXT,

    CONSTRAINT fk_ficha_treino_aluno FOREIGN KEY (fk_aluno) REFERENCES aluno(id_aluno) ON DELETE CASCADE,
    CONSTRAINT fk_ficha_treino_professor FOREIGN KEY (fk_professor) REFERENCES professor(id_professor)
);

-- TABELA: EXERCICIO_TREINO
CREATE TABLE exercicio_treino (
    id_exercicio INT AUTO_INCREMENT PRIMARY KEY,
    fk_ficha INT NOT NULL,
    nome_maquina_exercicio VARCHAR(100) NOT NULL,
    series INT NOT NULL,
    repeticoes VARCHAR(30) NOT NULL,
    carga VARCHAR(30) NOT NULL,
    tempo_descanso VARCHAR(30) NOT NULL,

    CONSTRAINT fk_exercicio_treino_ficha FOREIGN KEY (fk_ficha) REFERENCES ficha_treino(id_ficha) ON DELETE CASCADE
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


INSERT INTO unidade (nome, cnpj, endereco, telefone, status)
VALUES ('Unidade Sistema (Root)', NULL, NULL, NULL, TRUE);

INSERT INTO pessoa (nome, sexo, data_nascimento, profissao, contato, email, estilo_vida, atividade_fisica, tabagismo, alcool)
VALUES ('Root', 'OUTRO', '2000-01-01', 'Superusuário', NULL, 'root@user.com', NULL, NULL, 0, 0);

INSERT INTO usuario (fk_pessoa, fk_perfil, fk_unidade, login, senha_hash, status_aprovacao, ativo)
VALUES (
    (SELECT id_pessoa FROM pessoa WHERE email = 'root@user.com'),
    4, -- ROOT
    (SELECT id_unidade FROM unidade WHERE nome = 'Unidade Sistema (Root)'),
    'root@user.com',
    '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', -- 123senha
    'APROVADO',
    1
);


-- ============================================================================
-- MOCKS DE DEMONSTRAÇÃO
-- Unidades, admins, professores, alunos, avaliações, fichas de composição
-- corporal e fichas de treino, só pra ter um banco populado pra visualizar
-- todas as telas do sistema sem precisar cadastrar tudo manualmente.
--
-- Todo usuário mockado (admin/professor/aluno) usa a MESMA senha: 123senha
-- O login sempre é feito pelo e-mail (ver usuario::loginUsuario, que
-- consulta por pessoa.email — a coluna usuario.login não é usada no login,
-- mas é preenchida igual ao e-mail por consistência).
--
-- Lista de logins mockados (senha "123senha" para todos, inclusive o Root):
--   ROOT
--     root@user.com
--   ADMIN
--     admin.centro@pibeu.com      (Unidade: Academia PIBEU - Centro)
--     admin.zonasul@pibeu.com     (Unidade: Academia PIBEU - Zona Sul)
--   PROFESSOR
--     carla.professora@pibeu.com  (Academia PIBEU - Centro)
--     rafael.professor@pibeu.com  (Academia PIBEU - Centro)
--     fernanda.professora@pibeu.com (Academia PIBEU - Zona Sul)
--   ALUNO
--     sergio.aluno@pibeu.com    (Academia PIBEU - Centro — tem ficha de composição corporal e ficha de treino)
--     marina.aluno@pibeu.com    (Academia PIBEU - Centro — tem ficha de composição corporal e ficha de treino)
--     pedro.aluno@pibeu.com     (Academia PIBEU - Centro)
--     juliana.aluno@pibeu.com   (Academia PIBEU - Centro)
--     ricardo.aluno@pibeu.com   (Academia PIBEU - Zona Sul — tem ficha de composição corporal)
--     beatriz.aluno@pibeu.com   (Academia PIBEU - Zona Sul)
-- ============================================================================

-- ---------- Unidades ----------
INSERT INTO unidade (nome, cnpj, endereco, telefone, status) VALUES
('Academia PIBEU - Centro', '11222333000181', 'Av. Frei Serafim, 1500 - Centro, Teresina/PI', '(86) 3221-4455', TRUE),
('Academia PIBEU - Zona Sul', '22333444000162', 'Av. Raul Lopes, 2200 - Zona Sul, Teresina/PI', '(86) 3233-5566', TRUE);

-- ---------- Admins ----------
INSERT INTO pessoa (nome, sexo, data_nascimento, profissao, contato, email, estilo_vida, atividade_fisica, tabagismo, alcool) VALUES
('Ana Ferreira', 'F', '1980-03-15', 'Administradora', '(86) 99111-0001', 'admin.centro@pibeu.com', 'Ativo', 'Caminhada', 0, 0),
('Bruno Castro', 'M', '1979-07-22', 'Administrador', '(86) 99111-0002', 'admin.zonasul@pibeu.com', 'Ativo', 'Ciclismo', 0, 0);

INSERT INTO usuario (fk_pessoa, fk_perfil, fk_unidade, login, senha_hash, status_aprovacao, ativo) VALUES
((SELECT id_pessoa FROM pessoa WHERE email = 'admin.centro@pibeu.com'), 1,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Centro'),
 'admin.centro@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1),
((SELECT id_pessoa FROM pessoa WHERE email = 'admin.zonasul@pibeu.com'), 1,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Zona Sul'),
 'admin.zonasul@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1);

-- ---------- Professores ----------
INSERT INTO pessoa (nome, sexo, data_nascimento, profissao, contato, email, estilo_vida, atividade_fisica, tabagismo, alcool) VALUES
('Carla Souza', 'F', '1988-04-10', 'Educadora Física', '(86) 99222-0001', 'carla.professora@pibeu.com', 'Ativo', 'Musculação', 0, 0),
('Rafael Menezes', 'M', '1985-01-25', 'Educador Físico', '(86) 99222-0002', 'rafael.professor@pibeu.com', 'Ativo', 'Corrida', 0, 0),
('Fernanda Lima', 'F', '1990-06-02', 'Educadora Física', '(86) 99222-0003', 'fernanda.professora@pibeu.com', 'Ativo', 'Crossfit', 0, 0);

INSERT INTO usuario (fk_pessoa, fk_perfil, fk_unidade, login, senha_hash, status_aprovacao, ativo) VALUES
((SELECT id_pessoa FROM pessoa WHERE email = 'carla.professora@pibeu.com'), 2,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Centro'),
 'carla.professora@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1),
((SELECT id_pessoa FROM pessoa WHERE email = 'rafael.professor@pibeu.com'), 2,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Centro'),
 'rafael.professor@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1),
((SELECT id_pessoa FROM pessoa WHERE email = 'fernanda.professora@pibeu.com'), 2,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Zona Sul'),
 'fernanda.professora@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1);

INSERT INTO professor (fk_pessoa, cref, especialidade) VALUES
((SELECT id_pessoa FROM pessoa WHERE email = 'carla.professora@pibeu.com'), '012345-G/PI', 'Musculação e Emagrecimento'),
((SELECT id_pessoa FROM pessoa WHERE email = 'rafael.professor@pibeu.com'), '054321-G/PI', 'Treinamento Funcional'),
((SELECT id_pessoa FROM pessoa WHERE email = 'fernanda.professora@pibeu.com'), '098765-G/PI', 'Fisiologia do Exercício');

-- ---------- Alunos ----------
INSERT INTO pessoa (nome, sexo, data_nascimento, profissao, contato, email, estilo_vida, atividade_fisica, tabagismo, alcool) VALUES
('Sergio Barros', 'M', '1975-03-12', 'Comerciante', '(86) 99333-0001', 'sergio.aluno@pibeu.com', 'Moderado', 'Musculação 4x/semana', 0, 1),
('Marina Oliveira', 'F', '1990-07-22', 'Analista de Sistemas', '(86) 99333-0002', 'marina.aluno@pibeu.com', 'Ativo', 'Musculação e corrida', 0, 0),
('Pedro Almeida', 'M', '1998-11-05', 'Estudante', '(86) 99333-0003', 'pedro.aluno@pibeu.com', 'Sedentário', NULL, 0, 0),
('Juliana Costa', 'F', '2001-02-14', 'Designer', '(86) 99333-0004', 'juliana.aluno@pibeu.com', 'Moderado', 'Pilates', 0, 0),
('Ricardo Nunes', 'M', '1982-09-30', 'Engenheiro', '(86) 99333-0005', 'ricardo.aluno@pibeu.com', 'Ativo', 'Crossfit 3x/semana', 0, 1),
('Beatriz Rocha', 'F', '1995-05-18', 'Advogada', '(86) 99333-0006', 'beatriz.aluno@pibeu.com', 'Moderado', 'Yoga', 0, 0);

INSERT INTO usuario (fk_pessoa, fk_perfil, fk_unidade, login, senha_hash, status_aprovacao, ativo) VALUES
((SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'), 3,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Centro'),
 'sergio.aluno@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1),
((SELECT id_pessoa FROM pessoa WHERE email = 'marina.aluno@pibeu.com'), 3,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Centro'),
 'marina.aluno@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1),
((SELECT id_pessoa FROM pessoa WHERE email = 'pedro.aluno@pibeu.com'), 3,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Centro'),
 'pedro.aluno@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1),
((SELECT id_pessoa FROM pessoa WHERE email = 'juliana.aluno@pibeu.com'), 3,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Centro'),
 'juliana.aluno@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1),
((SELECT id_pessoa FROM pessoa WHERE email = 'ricardo.aluno@pibeu.com'), 3,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Zona Sul'),
 'ricardo.aluno@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1),
((SELECT id_pessoa FROM pessoa WHERE email = 'beatriz.aluno@pibeu.com'), 3,
 (SELECT id_unidade FROM unidade WHERE nome = 'Academia PIBEU - Zona Sul'),
 'beatriz.aluno@pibeu.com', '$2y$10$ih/0.V./2PbAGAzVkWdGfOj810V.Sb4LVAJTzkxakRnajWidbpdxm', 'APROVADO', 1);

INSERT INTO aluno (fk_pessoa, observacoes) VALUES
((SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'), 'Foco em emagrecimento e condicionamento.'),
((SELECT id_pessoa FROM pessoa WHERE email = 'marina.aluno@pibeu.com'), 'Foco em hipertrofia e performance.'),
((SELECT id_pessoa FROM pessoa WHERE email = 'pedro.aluno@pibeu.com'), 'Iniciante, sem histórico de treino.'),
((SELECT id_pessoa FROM pessoa WHERE email = 'juliana.aluno@pibeu.com'), 'Foco em flexibilidade e postura.'),
((SELECT id_pessoa FROM pessoa WHERE email = 'ricardo.aluno@pibeu.com'), 'Atleta amador de crossfit.'),
((SELECT id_pessoa FROM pessoa WHERE email = 'beatriz.aluno@pibeu.com'), 'Foco em bem-estar geral.');

INSERT INTO prontuario (fk_aluno, data_abertura, observacoes_gerais) VALUES
((SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com')), '2026-06-01', 'Sem restrições médicas relevantes.'),
((SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'marina.aluno@pibeu.com')), '2026-06-05', 'Sem restrições médicas relevantes.'),
((SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'pedro.aluno@pibeu.com')), '2026-07-10', 'Sem restrições médicas relevantes.'),
((SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'juliana.aluno@pibeu.com')), '2026-07-12', 'Sem restrições médicas relevantes.'),
((SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'ricardo.aluno@pibeu.com')), '2026-05-20', 'Histórico de lesão no ombro direito (recuperado).'),
((SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'beatriz.aluno@pibeu.com')), '2026-07-01', 'Sem restrições médicas relevantes.');

-- ---------- Avaliações físicas (progressivas, para popular o comparativo) ----------

-- Sergio Barros — 3 avaliações
INSERT INTO avaliacao (
    fk_prontuario, fk_professor, data_avaliacao,
    frequencia_cardiaca, pressao_arterial, sedentario, atividade_fisica, tabagismo, alcool,
    medicacao_controlada, problema_osteoarticular, problema_neuromuscular, problema_coronario, problema_vascular,
    hospitalizacao_5_anos, cirurgia_5_anos,
    torax, cintura, abdominal, quadril,
    braco_relaxado_direito, braco_relaxado_esquerdo, braco_contraido_direito, braco_contraido_esquerdo,
    coxa_direita, coxa_esquerda, panturrilha_direita, panturrilha_esquerda,
    peso, percentual_gordura, massa_magra, massa_muscular, agua_corporal, imc, taxa_metabolica_basal
) VALUES
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'carla.professora@pibeu.com')),
 '2026-06-10', '74 bpm', '128/84', 0, 'Musculação 3x/semana', 0, 1, 0,0,0,0,0,0,0,
 101.0, 96.0, 98.0, 104.0, 33.0, 32.5, 35.0, 34.5, 55.0, 54.5, 37.0, 36.5,
 71.2, 24.8, 53.5, 30.1, 54.0, 24.7, 1560.0),
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'carla.professora@pibeu.com')),
 '2026-07-15', '71 bpm', '124/82', 0, 'Musculação 4x/semana', 0, 1, 0,0,0,0,0,0,0,
 100.0, 92.0, 95.0, 104.0, 33.5, 33.0, 35.8, 35.2, 55.8, 55.2, 37.4, 36.8,
 69.5, 23.1, 53.9, 30.9, 54.8, 24.1, 1590.0),
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'carla.professora@pibeu.com')),
 '2026-08-27', '68 bpm', '120/80', 0, 'Musculação 4x/semana', 0, 1, 0,0,0,0,0,0,0,
 99.0, 89.0, 91.0, 103.0, 34.0, 33.5, 36.2, 35.6, 56.2, 55.8, 37.8, 37.2,
 67.7, 22.4, 54.6, 31.5, 55.6, 23.7, 1620.0);

-- Marina Oliveira — 2 avaliações
INSERT INTO avaliacao (
    fk_prontuario, fk_professor, data_avaliacao,
    frequencia_cardiaca, pressao_arterial, sedentario, atividade_fisica, tabagismo, alcool,
    medicacao_controlada, problema_osteoarticular, problema_neuromuscular, problema_coronario, problema_vascular,
    hospitalizacao_5_anos, cirurgia_5_anos,
    torax, cintura, abdominal, quadril,
    braco_relaxado_direito, braco_relaxado_esquerdo, braco_contraido_direito, braco_contraido_esquerdo,
    coxa_direita, coxa_esquerda, panturrilha_direita, panturrilha_esquerda,
    peso, percentual_gordura, massa_magra, massa_muscular, agua_corporal, imc, taxa_metabolica_basal
) VALUES
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'marina.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'rafael.professor@pibeu.com')),
 '2026-06-20', '78 bpm', '110/72', 0, 'Musculação e corrida 5x/semana', 0, 0, 0,0,0,0,0,0,0,
 89.0, 71.0, 76.0, 97.0, 27.5, 27.0, 29.5, 29.0, 56.0, 55.5, 34.0, 33.5,
 61.5, 26.5, 45.2, 22.8, 50.1, 22.6, 1340.0),
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'marina.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'rafael.professor@pibeu.com')),
 '2026-08-25', '73 bpm', '108/70', 0, 'Musculação e corrida 5x/semana', 0, 0, 0,0,0,0,0,0,0,
 87.0, 68.0, 73.0, 96.0, 28.0, 27.5, 30.2, 29.6, 56.8, 56.2, 34.5, 34.0,
 59.8, 24.2, 45.9, 23.6, 51.2, 21.9, 1360.0);

-- Pedro Almeida — 2 avaliações
INSERT INTO avaliacao (
    fk_prontuario, fk_professor, data_avaliacao, frequencia_cardiaca, pressao_arterial, sedentario, tabagismo, alcool,
    torax, cintura, abdominal, quadril, peso, percentual_gordura, massa_magra, massa_muscular, agua_corporal, imc, taxa_metabolica_basal
) VALUES
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'pedro.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'carla.professora@pibeu.com')),
 '2026-07-15', '82 bpm', '118/78', 1, 0, 0, 96.0, 88.0, 90.0, 98.0, 78.0, 27.0, 56.9, 27.0, 52.0, 26.9, 1750.0),
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'pedro.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'carla.professora@pibeu.com')),
 '2026-08-20', '79 bpm', '116/76', 0, 0, 0, 95.0, 86.0, 88.0, 97.5, 76.5, 25.4, 57.1, 27.8, 52.9, 26.4, 1780.0);

-- Juliana Costa — 2 avaliações
INSERT INTO avaliacao (
    fk_prontuario, fk_professor, data_avaliacao, frequencia_cardiaca, pressao_arterial, sedentario, tabagismo, alcool,
    torax, cintura, abdominal, quadril, peso, percentual_gordura, massa_magra, massa_muscular, agua_corporal, imc, taxa_metabolica_basal
) VALUES
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'juliana.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'rafael.professor@pibeu.com')),
 '2026-07-18', '75 bpm', '112/74', 0, 0, 0, 84.0, 66.0, 70.0, 92.0, 58.0, 25.0, 43.5, 20.5, 50.0, 21.5, 1290.0),
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'juliana.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'rafael.professor@pibeu.com')),
 '2026-08-22', '73 bpm', '110/72', 0, 0, 0, 83.0, 64.0, 68.0, 91.5, 57.0, 23.8, 43.9, 21.0, 50.6, 21.1, 1305.0);

-- Ricardo Nunes — 3 avaliações
INSERT INTO avaliacao (
    fk_prontuario, fk_professor, data_avaliacao,
    frequencia_cardiaca, pressao_arterial, sedentario, atividade_fisica, tabagismo, alcool,
    torax, cintura, abdominal, quadril,
    braco_relaxado_direito, braco_relaxado_esquerdo, braco_contraido_direito, braco_contraido_esquerdo,
    coxa_direita, coxa_esquerda, panturrilha_direita, panturrilha_esquerda,
    peso, percentual_gordura, massa_magra, massa_muscular, agua_corporal, imc, taxa_metabolica_basal
) VALUES
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'ricardo.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'fernanda.professora@pibeu.com')),
 '2026-05-25', '70 bpm', '122/80', 0, 'Crossfit 3x/semana', 0, 1,
 104.0, 90.0, 92.0, 101.0, 35.0, 34.5, 38.0, 37.5, 58.0, 57.5, 39.0, 38.5,
 82.0, 18.5, 66.8, 36.0, 58.0, 25.8, 1780.0),
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'ricardo.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'fernanda.professora@pibeu.com')),
 '2026-07-05', '68 bpm', '120/78', 0, 'Crossfit 4x/semana', 0, 1,
 105.0, 88.0, 90.0, 100.0, 35.5, 35.0, 38.6, 38.0, 58.6, 58.1, 39.4, 38.9,
 81.0, 17.2, 67.1, 36.8, 58.6, 25.4, 1810.0),
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'ricardo.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'fernanda.professora@pibeu.com')),
 '2026-08-28', '65 bpm', '118/76', 0, 'Crossfit 4x/semana', 0, 1,
 106.0, 86.0, 88.0, 99.0, 36.0, 35.5, 39.2, 38.6, 59.2, 58.7, 39.8, 39.3,
 80.2, 15.8, 67.5, 37.6, 59.3, 25.1, 1840.0);

-- Beatriz Rocha — 2 avaliações
INSERT INTO avaliacao (
    fk_prontuario, fk_professor, data_avaliacao, frequencia_cardiaca, pressao_arterial, sedentario, tabagismo, alcool,
    torax, cintura, abdominal, quadril, peso, percentual_gordura, massa_magra, massa_muscular, agua_corporal, imc, taxa_metabolica_basal
) VALUES
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'beatriz.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'fernanda.professora@pibeu.com')),
 '2026-07-08', '76 bpm', '114/76', 0, 0, 0, 87.0, 70.0, 74.0, 96.0, 63.0, 27.5, 45.7, 21.5, 49.5, 23.4, 1350.0),
((SELECT id_prontuario FROM prontuario WHERE fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'beatriz.aluno@pibeu.com'))),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'fernanda.professora@pibeu.com')),
 '2026-08-24', '74 bpm', '112/74', 0, 0, 0, 86.0, 68.0, 72.0, 95.5, 62.0, 26.1, 45.8, 21.8, 50.0, 23.0, 1362.0);

-- ---------- Fichas de Composição Corporal (estilo InBody) ----------
-- Vinculadas à avaliação mais recente de cada aluno escolhido.

-- Sergio Barros
INSERT INTO ficha_composicao_corporal (
    fk_avaliacao, fk_professor, data_teste, hora_teste, id_equipamento,
    altura, sexo_referencia, idade_referencia,
    agua_corporal_total, agua_corporal_min, agua_corporal_max,
    proteina, proteina_min, proteina_max,
    minerais, minerais_min, minerais_max,
    massa_gordura, massa_gordura_min, massa_gordura_max,
    peso, peso_min, peso_max,
    massa_muscular_esqueletica, mme_min, mme_max,
    imc, imc_min, imc_max,
    percentual_gordura_corporal, pgc_min, pgc_max,
    peso_ideal, controle_peso, controle_gordura, controle_muscular, pontuacao_geral,
    relacao_cintura_quadril, nivel_gordura_visceral, angulo_fase,
    massa_livre_gordura, mlg_min, mlg_max,
    fator_atividade, taxa_metabolica_basal, tmb_min, tmb_max,
    grau_obesidade, grau_obesidade_min, grau_obesidade_max,
    smi, ingestao_calorica_recomendada,
    seg_magra_braco_esq_kg, seg_magra_braco_esq_pct, seg_magra_braco_dir_kg, seg_magra_braco_dir_pct,
    seg_magra_tronco_kg, seg_magra_tronco_pct, seg_magra_perna_esq_kg, seg_magra_perna_esq_pct,
    seg_magra_perna_dir_kg, seg_magra_perna_dir_pct,
    seg_gordura_braco_esq_kg, seg_gordura_braco_esq_pct, seg_gordura_braco_dir_kg, seg_gordura_braco_dir_pct,
    seg_gordura_tronco_kg, seg_gordura_tronco_pct, seg_gordura_perna_esq_kg, seg_gordura_perna_esq_pct,
    seg_gordura_perna_dir_kg, seg_gordura_perna_dir_pct,
    observacoes
) VALUES (
    (SELECT a.id_avaliacao FROM avaliacao a
        JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
        JOIN aluno al ON al.id_aluno = pr.fk_aluno
        JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
        WHERE pe.email = 'sergio.aluno@pibeu.com' AND a.data_avaliacao = '2026-08-27'),
    (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'carla.professora@pibeu.com')),
    '2026-08-27', '07:06:00', 'INBODY270S-0001',
    172.0, 'M', 51,
    39.8, 27.4, 33.5,
    10.9, 8.4, 10.3,
    3.80, 3.14, 3.84,
    15.1, 10.1, 20.2,
    67.7, 53.4, 72.2,
    29.2, 26.7, 32.7,
    22.9, 18.5, 24.9,
    22.4, 10.0, 20.0,
    65.1, -2.6, -3.2, 0.6, 76,
    0.86, 7, 5.5,
    52.6, 48.0, 58.0,
    1.55, 1620.0, 1489.0, 1737.0,
    104.0, 90.0, 110.0,
    9.9, 2511.0,
    3.05, 99.8, 3.17, 104.0,
    24.6, 101.3, 7.44, 87.8,
    7.50, 88.5,
    0.8, 149.3, 0.8, 140.4,
    8.3, 208.2, 2.0, 125.5,
    2.0, 125.5,
    'Evolução consistente nas últimas 3 avaliações — redução de gordura com ganho de massa muscular.'
);

-- Marina Oliveira
INSERT INTO ficha_composicao_corporal (
    fk_avaliacao, fk_professor, data_teste, hora_teste, id_equipamento,
    altura, sexo_referencia, idade_referencia,
    agua_corporal_total, agua_corporal_min, agua_corporal_max,
    proteina, proteina_min, proteina_max,
    minerais, minerais_min, minerais_max,
    massa_gordura, massa_gordura_min, massa_gordura_max,
    peso, peso_min, peso_max,
    massa_muscular_esqueletica, mme_min, mme_max,
    imc, imc_min, imc_max,
    percentual_gordura_corporal, pgc_min, pgc_max,
    peso_ideal, controle_peso, controle_gordura, controle_muscular, pontuacao_geral,
    relacao_cintura_quadril, nivel_gordura_visceral, angulo_fase,
    massa_livre_gordura, mlg_min, mlg_max,
    fator_atividade, taxa_metabolica_basal, tmb_min, tmb_max,
    grau_obesidade, grau_obesidade_min, grau_obesidade_max,
    smi, ingestao_calorica_recomendada,
    seg_magra_braco_esq_kg, seg_magra_braco_esq_pct, seg_magra_braco_dir_kg, seg_magra_braco_dir_pct,
    seg_magra_tronco_kg, seg_magra_tronco_pct, seg_magra_perna_esq_kg, seg_magra_perna_esq_pct,
    seg_magra_perna_dir_kg, seg_magra_perna_dir_pct,
    seg_gordura_braco_esq_kg, seg_gordura_braco_esq_pct, seg_gordura_braco_dir_kg, seg_gordura_braco_dir_pct,
    seg_gordura_tronco_kg, seg_gordura_tronco_pct, seg_gordura_perna_esq_kg, seg_gordura_perna_esq_pct,
    seg_gordura_perna_dir_kg, seg_gordura_perna_dir_pct,
    observacoes
) VALUES (
    (SELECT a.id_avaliacao FROM avaliacao a
        JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
        JOIN aluno al ON al.id_aluno = pr.fk_aluno
        JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
        WHERE pe.email = 'marina.aluno@pibeu.com' AND a.data_avaliacao = '2026-08-25'),
    (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'rafael.professor@pibeu.com')),
    '2026-08-25', '08:15:00', 'INBODY270S-0002',
    165.0, 'F', 36,
    31.5, 25.0, 30.0,
    9.2, 7.5, 9.5,
    3.10, 2.7, 3.3,
    14.5, 12.0, 22.0,
    59.8, 48.0, 65.0,
    23.6, 21.0, 27.0,
    22.0, 18.5, 24.9,
    24.2, 18.0, 28.0,
    59.9, 0.1, -0.5, 0.6, 81,
    0.71, 4, 6.1,
    45.3, 41.0, 50.0,
    1.725, 1360.0, 1250.0, 1470.0,
    99.8, 90.0, 110.0,
    8.7, 2346.0,
    2.15, 102.0, 2.20, 103.5,
    18.3, 100.8, 5.90, 98.2,
    5.95, 98.9,
    1.1, 118.0, 1.1, 118.0,
    6.9, 130.5, 2.6, 108.0,
    2.7, 109.5,
    'Boa evolução com aumento de massa muscular e leve redução de gordura.'
);

-- Ricardo Nunes
INSERT INTO ficha_composicao_corporal (
    fk_avaliacao, fk_professor, data_teste, hora_teste, id_equipamento,
    altura, sexo_referencia, idade_referencia,
    agua_corporal_total, agua_corporal_min, agua_corporal_max,
    proteina, proteina_min, proteina_max,
    minerais, minerais_min, minerais_max,
    massa_gordura, massa_gordura_min, massa_gordura_max,
    peso, peso_min, peso_max,
    massa_muscular_esqueletica, mme_min, mme_max,
    imc, imc_min, imc_max,
    percentual_gordura_corporal, pgc_min, pgc_max,
    peso_ideal, controle_peso, controle_gordura, controle_muscular, pontuacao_geral,
    relacao_cintura_quadril, nivel_gordura_visceral, angulo_fase,
    massa_livre_gordura, mlg_min, mlg_max,
    fator_atividade, taxa_metabolica_basal, tmb_min, tmb_max,
    grau_obesidade, grau_obesidade_min, grau_obesidade_max,
    smi, ingestao_calorica_recomendada,
    seg_magra_braco_esq_kg, seg_magra_braco_esq_pct, seg_magra_braco_dir_kg, seg_magra_braco_dir_pct,
    seg_magra_tronco_kg, seg_magra_tronco_pct, seg_magra_perna_esq_kg, seg_magra_perna_esq_pct,
    seg_magra_perna_dir_kg, seg_magra_perna_dir_pct,
    seg_gordura_braco_esq_kg, seg_gordura_braco_esq_pct, seg_gordura_braco_dir_kg, seg_gordura_braco_dir_pct,
    seg_gordura_tronco_kg, seg_gordura_tronco_pct, seg_gordura_perna_esq_kg, seg_gordura_perna_esq_pct,
    seg_gordura_perna_dir_kg, seg_gordura_perna_dir_pct,
    observacoes
) VALUES (
    (SELECT a.id_avaliacao FROM avaliacao a
        JOIN prontuario pr ON pr.id_prontuario = a.fk_prontuario
        JOIN aluno al ON al.id_aluno = pr.fk_aluno
        JOIN pessoa pe ON pe.id_pessoa = al.fk_pessoa
        WHERE pe.email = 'ricardo.aluno@pibeu.com' AND a.data_avaliacao = '2026-08-28'),
    (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'fernanda.professora@pibeu.com')),
    '2026-08-28', '17:40:00', 'INBODY270S-0001',
    178.0, 'M', 43,
    45.1, 33.0, 40.0,
    13.2, 10.5, 13.0,
    4.30, 3.5, 4.3,
    12.7, 12.0, 24.0,
    80.2, 62.0, 84.0,
    37.6, 32.0, 39.0,
    25.3, 18.5, 24.9,
    15.8, 10.0, 20.0,
    69.7, -10.5, -8.6, -1.9, 88,
    0.87, 5, 7.2,
    67.5, 58.0, 70.0,
    1.9, 1840.0, 1690.0, 1990.0,
    115.1, 90.0, 110.0,
    11.9, 3496.0,
    3.85, 108.0, 3.90, 109.0,
    30.2, 106.5, 9.40, 96.5,
    9.45, 97.0,
    0.7, 95.0, 0.7, 95.0,
    6.8, 118.0, 2.4, 105.0,
    2.4, 105.0,
    'Atleta com baixo percentual de gordura e alta massa muscular — perfil de performance.'
);

-- ---------- Fichas de Treino (para a tela "Meus Treinos") ----------

INSERT INTO ficha_treino (fk_aluno, fk_professor, nome_treino, data_criacao, data_validade, observacoes) VALUES
((SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com')),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'carla.professora@pibeu.com')),
 'Treino A - Superior', '2026-08-27', '2026-10-27', 'Foco em membros superiores e core.'),
((SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com')),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'carla.professora@pibeu.com')),
 'Treino B - Inferior', '2026-08-27', '2026-10-27', 'Foco em membros inferiores.'),
((SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'marina.aluno@pibeu.com')),
 (SELECT id_professor FROM professor WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'rafael.professor@pibeu.com')),
 'Treino Full Body - Hipertrofia', '2026-08-25', '2026-10-25', 'Full body 3x/semana, foco em hipertrofia.');

INSERT INTO exercicio_treino (fk_ficha, nome_maquina_exercicio, series, repeticoes, carga, tempo_descanso) VALUES
((SELECT id_ficha FROM ficha_treino WHERE nome_treino = 'Treino A - Superior' AND fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'))),
 'Supino reto', 4, '10-12', '40kg', '60s'),
((SELECT id_ficha FROM ficha_treino WHERE nome_treino = 'Treino A - Superior' AND fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'))),
 'Puxada frontal', 4, '10-12', '35kg', '60s'),
((SELECT id_ficha FROM ficha_treino WHERE nome_treino = 'Treino A - Superior' AND fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'))),
 'Rosca direta', 3, '12-15', '15kg', '45s'),
((SELECT id_ficha FROM ficha_treino WHERE nome_treino = 'Treino B - Inferior' AND fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'))),
 'Leg press', 4, '10-12', '120kg', '90s'),
((SELECT id_ficha FROM ficha_treino WHERE nome_treino = 'Treino B - Inferior' AND fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'))),
 'Cadeira extensora', 3, '12-15', '35kg', '60s'),
((SELECT id_ficha FROM ficha_treino WHERE nome_treino = 'Treino B - Inferior' AND fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'sergio.aluno@pibeu.com'))),
 'Mesa flexora', 3, '12-15', '30kg', '60s'),
((SELECT id_ficha FROM ficha_treino WHERE nome_treino = 'Treino Full Body - Hipertrofia' AND fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'marina.aluno@pibeu.com'))),
 'Agachamento livre', 4, '8-10', '50kg', '90s'),
((SELECT id_ficha FROM ficha_treino WHERE nome_treino = 'Treino Full Body - Hipertrofia' AND fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'marina.aluno@pibeu.com'))),
 'Remada curvada', 4, '10-12', '30kg', '60s'),
((SELECT id_ficha FROM ficha_treino WHERE nome_treino = 'Treino Full Body - Hipertrofia' AND fk_aluno = (SELECT id_aluno FROM aluno WHERE fk_pessoa = (SELECT id_pessoa FROM pessoa WHERE email = 'marina.aluno@pibeu.com'))),
 'Desenvolvimento de ombros', 3, '10-12', '18kg', '60s');
