/**
 * Funções de cálculo da Ficha de Composição Corporal (estilo InBody).
 *
 * Usado tanto no formulário de preenchimento (cálculo ao vivo enquanto o
 * professor digita) quanto, futuramente, por uma rotina de importação
 * automática da balança InBody 270S — os dois cenários preenchem os
 * mesmos campos, então a lógica de cálculo fica centralizada aqui.
 *
 * Onde a InBody usa tabelas proprietárias (faixas de referência de água
 * corporal, proteína, minerais, score geral, grau/ângulo de fase, nível
 * de gordura visceral), não existe fórmula pública confiável — esses
 * valores continuam sendo digitados manualmente pelo professor.
 */
(function (global) {
    'use strict';

    function paraNumero(v) {
        if (v === null || v === undefined || v === '') return null;
        const n = parseFloat(String(v).replace(',', '.'));
        return Number.isFinite(n) ? n : null;
    }

    function alturaEmMetros(alturaCm) {
        const a = paraNumero(alturaCm);
        return a ? a / 100 : null;
    }

    /** IMC = peso(kg) / altura(m)^2 */
    function imc(pesoKg, alturaCm) {
        const peso = paraNumero(pesoKg);
        const altura = alturaEmMetros(alturaCm);
        if (!peso || !altura) return null;
        return peso / (altura * altura);
    }

    /**
     * Taxa Metabólica Basal — fórmula de Mifflin-St Jeor (mais precisa que
     * Harris-Benedict para a população geral, referência usada por
     * nutricionistas/educadores físicos).
     */
    function taxaMetabolicaBasal(pesoKg, alturaCm, idadeAnos, sexo) {
        const peso = paraNumero(pesoKg);
        const altura = paraNumero(alturaCm);
        const idade = paraNumero(idadeAnos);
        if (!peso || !altura || !idade) return null;

        const base = 10 * peso + 6.25 * altura - 5 * idade;
        return sexo === 'F' ? base - 161 : base + 5;
    }

    /** Ingestão calórica recomendada = TMB x fator de atividade (Harris-Benedict PAL). */
    function ingestaoCalorica(tmb, fatorAtividade) {
        const t = paraNumero(tmb);
        const f = paraNumero(fatorAtividade);
        if (!t || !f) return null;
        return t * f;
    }

    /** Peso ideal = IMC-alvo x altura(m)^2. IMC-alvo default 22 (meio da faixa saudável da OMS). */
    function pesoIdeal(alturaCm, imcAlvo) {
        const altura = alturaEmMetros(alturaCm);
        const alvo = paraNumero(imcAlvo) || 22;
        if (!altura) return null;
        return alvo * altura * altura;
    }

    /** Massa livre de gordura = peso total - massa de gordura. */
    function massaLivreGordura(pesoKg, massaGorduraKg) {
        const peso = paraNumero(pesoKg);
        const gordura = paraNumero(massaGorduraKg);
        if (peso === null || gordura === null) return null;
        return peso - gordura;
    }

    /** Grau de obesidade (%) = peso atual / peso ideal x 100. Faixa normal de referência: 90~110%. */
    function grauObesidade(pesoKg, pesoIdealKg) {
        const peso = paraNumero(pesoKg);
        const ideal = paraNumero(pesoIdealKg);
        if (!peso || !ideal) return null;
        return (peso / ideal) * 100;
    }

    /** SMI (Skeletal Muscle Mass Index) = massa muscular esquelética(kg) / altura(m)^2. */
    function smi(mmeKg, alturaCm) {
        const mme = paraNumero(mmeKg);
        const altura = alturaEmMetros(alturaCm);
        if (!mme || !altura) return null;
        return mme / (altura * altura);
    }

    /** Relação cintura-quadril = cintura(cm) / quadril(cm). */
    function relacaoCinturaQuadril(cinturaCm, quadrilCm) {
        const cintura = paraNumero(cinturaCm);
        const quadril = paraNumero(quadrilCm);
        if (!cintura || !quadril) return null;
        return cintura / quadril;
    }

    /**
     * Controle de peso = peso ideal - peso atual.
     * Controle de gordura = massa de gordura ideal - massa de gordura atual
     *   (massa de gordura ideal = peso ideal x %gordura-alvo média).
     * Controle muscular = controle de peso - controle de gordura
     *   (mantém a mesma identidade da InBody: controle de peso é a soma
     *   dos outros dois controles).
     */
    function controles(pesoKg, pesoIdealKg, massaGorduraKg, pgcAlvoPercentual) {
        const peso = paraNumero(pesoKg);
        const ideal = paraNumero(pesoIdealKg);
        const gordura = paraNumero(massaGorduraKg);
        const alvo = paraNumero(pgcAlvoPercentual);

        if (peso === null || ideal === null) {
            return { controlePeso: null, controleGordura: null, controleMuscular: null };
        }

        const controlePeso = ideal - peso;

        if (gordura === null || alvo === null) {
            return { controlePeso, controleGordura: null, controleMuscular: null };
        }

        const massaGorduraIdeal = ideal * (alvo / 100);
        const controleGordura = massaGorduraIdeal - gordura;
        const controleMuscular = controlePeso - controleGordura;

        return { controlePeso, controleGordura, controleMuscular };
    }

    /**
     * Percentual de posição na barra (estilo InBody), aproximando 100%
     * como o centro da faixa normal. É uma aproximação pública — a InBody
     * usa uma curva proprietária — mas mantém a mesma leitura visual
     * (abaixo / normal / acima da faixa).
     */
    function percentualNaFaixa(valor, min, max) {
        const v = paraNumero(valor);
        const mn = paraNumero(min);
        const mx = paraNumero(max);
        if (v === null || !mn || !mx) return null;
        const centro = (mn + mx) / 2;
        if (!centro) return null;
        return (v / centro) * 100;
    }

    /** Idade em anos completos a partir da data de nascimento e da data do teste. */
    function idade(dataNascimentoISO, dataReferenciaISO) {
        if (!dataNascimentoISO) return null;
        const nascimento = new Date(dataNascimentoISO);
        const referencia = dataReferenciaISO ? new Date(dataReferenciaISO) : new Date();
        if (isNaN(nascimento.getTime()) || isNaN(referencia.getTime())) return null;

        let anos = referencia.getFullYear() - nascimento.getFullYear();
        const m = referencia.getMonth() - nascimento.getMonth();
        if (m < 0 || (m === 0 && referencia.getDate() < nascimento.getDate())) {
            anos--;
        }
        return anos;
    }

    /**
     * Faixas normais de referência sugeridas (ponto de partida editável
     * pelo professor). IMC segue a classificação da OMS; %Gordura segue
     * referência do American Council on Exercise (ACE) por sexo.
     */
    const FAIXAS_PADRAO = {
        imc: { min: 18.5, max: 24.9 },
        pgc: {
            M: { min: 10, max: 20 },
            F: { min: 18, max: 28 },
        },
        grauObesidade: { min: 90, max: 110 },
    };

    /**
     * Gasto calórico estimado por esporte em 30 minutos, a partir do peso
     * do aluno. MET = equivalente metabólico (Compendium of Physical
     * Activities, valores de domínio público). kcal = MET x peso(kg) x horas.
     */
    const MET_ESPORTES = {
        'Golfe': 4.8,
        'Gate-ball': 4.0,
        'Caminhada': 3.5,
        'Ioga': 3.0,
        'Badminton': 5.5,
        'Tênis de mesa': 4.0,
        'Tênis': 7.3,
        'Ciclismo': 7.5,
        'Boxe': 7.8,
        'Basquetebol': 8.0,
        'Escalada': 8.0,
        'Aeróbica': 7.3,
        'Jogging': 7.0,
        'Futebol': 7.0,
        'Natação': 6.0,
        'Esgrima japonesa': 6.0,
        'Raquetebol': 7.0,
        'Squash': 12.0,
        'Taekwondo': 10.3,
        'Pular corda': 10.0,
    };

    function gastoCaloricoPorEsporte(pesoKg, minutos) {
        const peso = paraNumero(pesoKg);
        const tempoHoras = (paraNumero(minutos) || 30) / 60;
        if (!peso) return {};

        const resultado = {};
        Object.keys(MET_ESPORTES).forEach(function (esporte) {
            resultado[esporte] = Math.round(MET_ESPORTES[esporte] * peso * tempoHoras);
        });
        return resultado;
    }

    global.ComposicaoCalc = {
        paraNumero,
        imc,
        taxaMetabolicaBasal,
        ingestaoCalorica,
        pesoIdeal,
        massaLivreGordura,
        grauObesidade,
        smi,
        relacaoCinturaQuadril,
        controles,
        percentualNaFaixa,
        idade,
        gastoCaloricoPorEsporte,
        FAIXAS_PADRAO,
        MET_ESPORTES,
    };

})(window);
