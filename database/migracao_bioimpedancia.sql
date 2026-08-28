-- Migração: adiciona campos de bioimpedância na tabela avaliacao
-- Rodar em bancos já existentes (quem for criar o banco do zero, já usa
-- o script_pibeu_definitivo_2026.sql atualizado e não precisa rodar isso).

ALTER TABLE avaliacao
    ADD COLUMN peso DECIMAL(5,2) AFTER panturrilha_esquerda,
    ADD COLUMN percentual_gordura DECIMAL(5,2) AFTER peso,
    ADD COLUMN massa_magra DECIMAL(5,2) AFTER percentual_gordura,
    ADD COLUMN massa_muscular DECIMAL(5,2) AFTER massa_magra,
    ADD COLUMN agua_corporal DECIMAL(5,2) AFTER massa_muscular,
    ADD COLUMN imc DECIMAL(5,2) AFTER agua_corporal,
    ADD COLUMN taxa_metabolica_basal DECIMAL(6,2) AFTER imc;
