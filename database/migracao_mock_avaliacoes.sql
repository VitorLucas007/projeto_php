-- Mocks de avaliação para testar a tela de comparativo
-- (app/views/avaliacao/view.avaliacao.comparativo.php).
--
-- Cria 3 avaliações em sequência (datas 10, 20 e 27/08/2026) para o mesmo
-- aluno/prontuário usado nos mocks de ficha de treino, com evolução
-- progressiva nas medidas de bioimpedância e circunferências — o suficiente
-- para o comparativo (que exige pelo menos 2 avaliações) mostrar linhas de
-- "melhora", "piora" e "sem variação" no gráfico e na tabela.
--
-- Usa o primeiro prontuário e o primeiro professor já cadastrados no banco;
-- se nenhum dos dois existir ainda, os INSERTs não inserem nada (sem erro).

INSERT INTO avaliacao (
    fk_prontuario, fk_professor, data_avaliacao,
    frequencia_cardiaca, pressao_arterial, sedentario, atividade_fisica, tabagismo, alcool,
    torax, cintura, abdominal, quadril,
    braco_relaxado_direito, braco_relaxado_esquerdo, braco_contraido_direito, braco_contraido_esquerdo,
    coxa_direita, coxa_esquerda, panturrilha_direita, panturrilha_esquerda,
    peso, percentual_gordura, massa_magra, massa_muscular, agua_corporal, imc, taxa_metabolica_basal
)
SELECT pr.id_prontuario, p.id_professor, '2026-08-10',
       '72 bpm', '130/85', 0, 'Musculação 3x/semana', 0, 0,
       102.0, 98.0, 100.0, 105.0,
       32.0, 31.5, 34.0, 33.5,
       54.0, 53.5, 36.0, 35.5,
       92.5, 28.5, 65.0, 30.0, 55.0, 29.8, 1650.0
FROM prontuario pr
JOIN professor p ON TRUE
ORDER BY pr.id_prontuario, p.id_professor
LIMIT 1;

INSERT INTO avaliacao (
    fk_prontuario, fk_professor, data_avaliacao,
    frequencia_cardiaca, pressao_arterial, sedentario, atividade_fisica, tabagismo, alcool,
    torax, cintura, abdominal, quadril,
    braco_relaxado_direito, braco_relaxado_esquerdo, braco_contraido_direito, braco_contraido_esquerdo,
    coxa_direita, coxa_esquerda, panturrilha_direita, panturrilha_esquerda,
    peso, percentual_gordura, massa_magra, massa_muscular, agua_corporal, imc, taxa_metabolica_basal
)
SELECT pr.id_prontuario, p.id_professor, '2026-08-20',
       '68 bpm', '124/80', 0, 'Musculação 4x/semana', 0, 0,
       100.0, 93.0, 95.0, 104.0,
       32.5, 32.0, 34.8, 34.2,
       55.0, 54.5, 36.5, 36.0,
       88.0, 25.0, 66.0, 31.5, 56.5, 28.4, 1700.0
FROM prontuario pr
JOIN professor p ON TRUE
ORDER BY pr.id_prontuario, p.id_professor
LIMIT 1;

INSERT INTO avaliacao (
    fk_prontuario, fk_professor, data_avaliacao,
    frequencia_cardiaca, pressao_arterial, sedentario, atividade_fisica, tabagismo, alcool,
    torax, cintura, abdominal, quadril,
    braco_relaxado_direito, braco_relaxado_esquerdo, braco_contraido_direito, braco_contraido_esquerdo,
    coxa_direita, coxa_esquerda, panturrilha_direita, panturrilha_esquerda,
    peso, percentual_gordura, massa_magra, massa_muscular, agua_corporal, imc, taxa_metabolica_basal
)
SELECT pr.id_prontuario, p.id_professor, '2026-08-27',
       '65 bpm', '118/76', 0, 'Musculação 4x/semana', 0, 0,
       99.0, 89.0, 91.0, 103.0,
       33.0, 32.5, 35.5, 35.0,
       56.2, 55.8, 37.0, 36.5,
       85.2, 22.3, 67.2, 32.8, 58.0, 27.5, 1740.0
FROM prontuario pr
JOIN professor p ON TRUE
ORDER BY pr.id_prontuario, p.id_professor
LIMIT 1;
