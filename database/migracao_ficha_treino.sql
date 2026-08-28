-- Migração: cria as tabelas ficha_treino e exercicio_treino (feature "Meus Treinos")
-- Rodar em bancos já existentes (quem for criar o banco do zero, já usa
-- o script_pibeu_definitivo_2026.sql atualizado e não precisa rodar isso).
--
-- Causa raiz do erro "Fatal error: Uncaught RuntimeException... Erro ao processar
-- sua solicitação" ao abrir "Meus Treinos": o model/controller de ficha de treino
-- já existia no código, mas as tabelas nunca tinham sido criadas no banco, então
-- $db->prepare() falhava e o persistirBD lançava a exceção genérica.

CREATE TABLE IF NOT EXISTS ficha_treino (
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

CREATE TABLE IF NOT EXISTS exercicio_treino (
    id_exercicio INT AUTO_INCREMENT PRIMARY KEY,
    fk_ficha INT NOT NULL,
    nome_maquina_exercicio VARCHAR(100) NOT NULL,
    series INT NOT NULL,
    repeticoes VARCHAR(30) NOT NULL,
    carga VARCHAR(30) NOT NULL,
    tempo_descanso VARCHAR(30) NOT NULL,

    CONSTRAINT fk_exercicio_treino_ficha FOREIGN KEY (fk_ficha) REFERENCES ficha_treino(id_ficha) ON DELETE CASCADE
);


-- ============================================================================
-- MOCKS PARA COMPARAÇÃO
-- Cria duas fichas de treino (A e B) para o mesmo aluno, cada uma com seus
-- próprios exercícios, para servir de massa de teste ao comparar as telas
-- de "Meus Treinos" (aluno) e listagem/edição (professor).
-- Usa o primeiro aluno e o primeiro professor já cadastrados no banco;
-- se ainda não existir nenhum dos dois, os INSERTs abaixo simplesmente
-- não inserem nada (sem erro).
-- ============================================================================

INSERT INTO ficha_treino (fk_aluno, fk_professor, nome_treino, data_criacao, data_validade, observacoes)
SELECT a.id_aluno, p.id_professor, 'Treino A - Superior', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY),
       'Mock de comparação: foco em membros superiores.'
FROM aluno a
JOIN professor p ON TRUE
ORDER BY a.id_aluno, p.id_professor
LIMIT 1;

INSERT INTO ficha_treino (fk_aluno, fk_professor, nome_treino, data_criacao, data_validade, observacoes)
SELECT a.id_aluno, p.id_professor, 'Treino B - Inferior', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY),
       'Mock de comparação: foco em membros inferiores.'
FROM aluno a
JOIN professor p ON TRUE
ORDER BY a.id_aluno, p.id_professor
LIMIT 1;

INSERT INTO exercicio_treino (fk_ficha, nome_maquina_exercicio, series, repeticoes, carga, tempo_descanso)
SELECT id_ficha, 'Supino reto', 4, '10-12', '40kg', '60s'
FROM ficha_treino WHERE nome_treino = 'Treino A - Superior'
ORDER BY id_ficha DESC LIMIT 1;

INSERT INTO exercicio_treino (fk_ficha, nome_maquina_exercicio, series, repeticoes, carga, tempo_descanso)
SELECT id_ficha, 'Puxada frontal', 4, '10-12', '35kg', '60s'
FROM ficha_treino WHERE nome_treino = 'Treino A - Superior'
ORDER BY id_ficha DESC LIMIT 1;

INSERT INTO exercicio_treino (fk_ficha, nome_maquina_exercicio, series, repeticoes, carga, tempo_descanso)
SELECT id_ficha, 'Rosca direta', 3, '12-15', '15kg', '45s'
FROM ficha_treino WHERE nome_treino = 'Treino A - Superior'
ORDER BY id_ficha DESC LIMIT 1;

INSERT INTO exercicio_treino (fk_ficha, nome_maquina_exercicio, series, repeticoes, carga, tempo_descanso)
SELECT id_ficha, 'Leg press', 4, '10-12', '120kg', '90s'
FROM ficha_treino WHERE nome_treino = 'Treino B - Inferior'
ORDER BY id_ficha DESC LIMIT 1;

INSERT INTO exercicio_treino (fk_ficha, nome_maquina_exercicio, series, repeticoes, carga, tempo_descanso)
SELECT id_ficha, 'Cadeira extensora', 3, '12-15', '35kg', '60s'
FROM ficha_treino WHERE nome_treino = 'Treino B - Inferior'
ORDER BY id_ficha DESC LIMIT 1;

INSERT INTO exercicio_treino (fk_ficha, nome_maquina_exercicio, series, repeticoes, carga, tempo_descanso)
SELECT id_ficha, 'Mesa flexora', 3, '12-15', '30kg', '60s'
FROM ficha_treino WHERE nome_treino = 'Treino B - Inferior'
ORDER BY id_ficha DESC LIMIT 1;
