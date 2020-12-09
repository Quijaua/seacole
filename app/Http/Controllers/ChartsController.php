<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Khill\Lavacharts\Lavacharts;
use Lava;
use DB;
use Carbon\Carbon;
use App\Monitoramento;
use App\Paciente;

class ChartsController extends Controller
{
  public function index()
  {
    return view('graphs');
  }

  public function novos_casos_monitorados()
  {
    $novos_casos_monitorados_select = DB::select("
    SELECT
        CAST(CONCAT(SUBSTRING(data_inicio,7,4),'-',SUBSTRING(data_inicio,4,2),'-',SUBSTRING(data_inicio,1,2)) AS DATE) AS date
      , COUNT(id) AS value
    FROM
      (SELECT
        id
        , CASE
          WHEN LENGTH(data_inicio) = 8 THEN CONCAT(SUBSTRING(data_inicio,1,6),20,SUBSTRING(data_inicio,7,2))
          ELSE data_inicio
        END AS data_inicio
      FROM
        (SELECT
          id
          , CASE WHEN data_inicio_ac_psicologico IS NULL THEN data_inicio_monitoramento ELSE data_inicio_ac_psicologico END AS data_inicio
        FROM pacientes)TB)TBB
    GROUP BY 1
    ORDER BY 1;
    ");
    $novos_casos_monitorados = json_encode($novos_casos_monitorados_select);
    return $novos_casos_monitorados;
  }

  public function monitorados_exclusivo_psicologia()
  {
    $monitorados_exclusivo_psicologia_select = DB::select("
    SELECT
      monitoramento
        , SUM(litres) AS litres
    FROM
    (SELECT
        CASE
        WHEN agente_id IS NULL AND psicologo_id IS NOT NULL THEN 'Casos totais monitorados exclusivamente por equipe de psicologia'
      END AS monitoramento
        , CASE
        WHEN agente_id IS NULL AND psicologo_id IS NOT NULL THEN COUNT(id)
      END AS litres
    FROM pacientes
    GROUP BY agente_id, psicologo_id

    UNION ALL

    SELECT
        'Casos totais monitorados por agentes populares de saúde por equipe de psicologia' AS monitoramento
        , COUNT(id) AS litres
    FROM pacientes
    WHERE id NOT IN (SELECT id FROM pacientes WHERE (agente_id IS NULL AND psicologo_id IS NOT NULL))
    GROUP BY agente_id, psicologo_id
    )TB
    WHERE monitoramento IS NOT NULL
    GROUP BY monitoramento;
    ");
    $monitorados_exclusivo_psicologia = json_encode($monitorados_exclusivo_psicologia_select);
    return $monitorados_exclusivo_psicologia;
  }

  public function situacao_total_casos_monitorados()
  {
    $situacao_total_casos_monitorados_select = DB::select("
    SELECT
    	Case
    		WHEN situacao = 1 THEN 'Caso ativo GRAVE'
    		WHEN situacao = 2 THEN 'Caso ativo LEVE'
    		WHEN situacao = 3 THEN 'Contato caso confirmado - ativo'
    		WHEN situacao = 4 THEN 'Outras situações (sem relação com COVID-19) - ativos'
    		WHEN situacao = 5 THEN 'Exclusivo psicologia - ativo'
    		WHEN situacao = 6 THEN 'Monitoramento encerrado GRAVE - segue apenas com psicólogos'
    		WHEN situacao = 7 THEN 'Monitoramento encerrado LEVE - segue apenas com psicólogos'
    		WHEN situacao = 8 THEN 'Monitoramento encerrado contato - segue apenas com psicólogos'
    		WHEN situacao = 9 THEN 'Monitoramento encerrado outros - segue apenas com psicólogos'
    		WHEN situacao = 10 THEN 'Caso finalizado GRAVE'
    		WHEN situacao = 11 THEN 'Caso finalizado LEVE'
    		WHEN situacao = 12 THEN 'Contato com caso confirmado - finalizado'
    		WHEN situacao = 13 THEN 'Outras situações (sem relação com COVID-19) - finalizado'
    		WHEN situacao = 14 THEN 'Exclusivo psicologia - finalizado'
            ELSE 'Sem informação'
    	END AS situacao
        , COUNT(id) quantidade_casos
    FROM
    	pacientes
    GROUP BY situacao;
    ");
    $situacao_total_casos_monitorados = json_encode($situacao_total_casos_monitorados_select);
    return $situacao_total_casos_monitorados;
  }

  public function situacao_total_casos_monitorados_1()
  {
    $situacao_total_casos_monitorados_1_select = DB::select("
    SELECT
      Case
        WHEN situacao = 1 THEN 'Caso ativo GRAVE'
        WHEN situacao = 2 THEN 'Caso ativo LEVE'
        WHEN situacao = 3 THEN 'Contato caso confirmado - ativo'
        WHEN situacao = 4 THEN 'Outras situações (sem relação com COVID-19) - ativos'
        WHEN situacao = 5 THEN 'Exclusivo psicologia - ativo'
        WHEN situacao = 6 THEN 'Monitoramento encerrado GRAVE - segue apenas com psicólogos'
        WHEN situacao = 7 THEN 'Monitoramento encerrado LEVE - segue apenas com psicólogos'
        WHEN situacao = 8 THEN 'Monitoramento encerrado contato - segue apenas com psicólogos'
        WHEN situacao = 9 THEN 'Monitoramento encerrado outros - segue apenas com psicólogos'
        WHEN situacao = 10 THEN 'Caso finalizado GRAVE'
        WHEN situacao = 11 THEN 'Caso finalizado LEVE'
        WHEN situacao = 12 THEN 'Contato com caso confirmado - finalizado'
        WHEN situacao = 13 THEN 'Outras situações (sem relação com COVID-19) - finalizado'
        WHEN situacao = 14 THEN 'Exclusivo psicologia - finalizado'
            ELSE 'Sem informação'
      END AS situacao
        , COUNT(id) quantidade
    FROM
      pacientes
    GROUP BY situacao
    ORDER BY 2 DESC;
    ");
    $situacao_total_casos_monitorados_1 = json_encode($situacao_total_casos_monitorados_1_select);
    return $situacao_total_casos_monitorados_1;
  }

  public function casos_monitorados_por_cidade()
  {
    $casos_monitorados_por_cidade_select = DB::select("
    SELECT
      Case
        WHEN endereco_cidade IS NULL THEN 'Sem informação'
            ELSE endereco_cidade
      END AS endereco_cidade
      , COUNT(id) quantidade
    FROM
      pacientes
    GROUP BY endereco_cidade
    ORDER BY quantidade
    DESC;
    ");
    $casos_monitorados_por_cidade = json_encode($casos_monitorados_por_cidade_select);
    return $casos_monitorados_por_cidade;
  }

  public function raca_cor_geral()
  {
    //CONSULTA GRÁFICO
    $graph_data_select = DB::select("
    SELECT
    	Case
    		WHEN cor_raca IS NULL THEN 'Sem informação'
            ELSE cor_raca
    	END AS cor_raca
    	, COUNT(id) quantidade
    FROM
    	pacientes
    GROUP BY cor_raca
    ORDER BY quantidade
    DESC;
    ");
    //CONSULTA LEGENDAS
    $legend_data_select = DB::select("
    SELECT CONCAT('Pessoas negras (pretas + parda) totalizam ',FORMAT((SUM(negros)*100)/(SELECT COUNT(id) FROM pacientes),2),'% do total.') AS legenda
    FROM
    (SELECT Case WHEN cor_raca IN ('Preta','Parda') THEN COUNT(id) END negros
    FROM pacientes
    GROUP BY cor_raca)TB;
    ");
    //$graph_data = json_encode($graph_data_select);
    //$legend_data = json_encode($legend_data_select);
    return array($graph_data_select,$legend_data_select);
  }

  public function genero_por_raca_cor()
  {
    //CONSULTA GRÁFICOS
    $genero_por_raca_cor_graph = DB::select("
    SELECT
      COALESCE(identidade_genero, 'Sem informação') AS identidade_genero
        , COALESCE(SUM(sem_informacao),0) AS sem_informacao
        , COALESCE(SUM(preta),0) AS preta
        , COALESCE(SUM(parda),0) AS parda
        , COALESCE(SUM(branca),0) AS branca
        , COALESCE(SUM(amarela),0) AS amarela
        , COALESCE(SUM(indigena),0) AS indigena
    FROM(
      SELECT
        identidade_genero
        , CASE WHEN cor_raca IS NULL THEN COUNT(id) END AS sem_informacao
        , CASE WHEN cor_raca = 'Preta' THEN COUNT(id) END AS preta
        , CASE WHEN cor_raca = 'Parda' THEN COUNT(id) END AS parda
        , CASE WHEN cor_raca = 'Branca' THEN COUNT(id) END AS branca
        , CASE WHEN cor_raca = 'Amarela' THEN COUNT(id) END AS amarela
        , CASE WHEN cor_raca = 'Indígena'THEN COUNT(id) END AS indigena
      FROM pacientes
      GROUP BY
        identidade_genero
        , cor_raca)TB
    GROUP BY
      identidade_genero;
    ");
    //CONSULTA LEGENDAS
    $genero_por_raca_cor_legend = DB::select("
    SELECT CONCAT('São',
    (SELECT COUNT(id) FROM pacientes WHERE cor_raca IN ('Preta','Parda') AND identidade_genero = 'mulher cis'),
    ' mulheres cis e ',
    (SELECT COUNT(id) FROM pacientes WHERE cor_raca IN ('Preta','Parda') AND identidade_genero = 'homem cis'),
    ' homens cis de Raça Negra (Preta + Parda)');
    ");
    return array($genero_por_raca_cor_graph,$genero_por_raca_cor_legend);
  }

  public function faixa_etaria_genero()
  {
    $faixa_etaria_genero_select = DB::select("
    SELECT
      idade
        , COALESCE(SUM(homem),0)* -1 AS homens
        , COALESCE(SUM(mulher),0) AS mulheres
    FROM
      (SELECT
        CASE
          WHEN idade <= 4 THEN '0-4'
          WHEN idade >= 5 AND idade <= 9 THEN '5-9'
          WHEN idade >= 10 AND idade <= 14 THEN '10-14'
          WHEN idade >= 15 AND idade <= 19 THEN '15-19'
          WHEN idade >= 20 AND idade <= 24 THEN '20-24'
          WHEN idade >= 25 AND idade <= 29 THEN '25-29'
          WHEN idade >= 30 AND idade <= 34 THEN '30-34'
          WHEN idade >= 35 AND idade <= 39 THEN '35-39'
          WHEN idade >= 40 AND idade <= 44 THEN '40-44'
          WHEN idade >= 45 AND idade <= 49 THEN '45-49'
          WHEN idade >= 50 AND idade <= 54 THEN '50-54'
          WHEN idade >= 55 AND idade <= 59 THEN '55-59'
          WHEN idade >= 60 AND idade <= 64 THEN '60-64'
          WHEN idade >= 65 AND idade <= 69 THEN '65-69'
          WHEN idade >= 70 AND idade <= 74 THEN '70-74'
          WHEN idade >= 75 AND idade <= 79 THEN '75-79'
          WHEN idade >= 80 AND idade <= 84 THEN '80-84'
          WHEN idade >= 85 AND idade <= 89 THEN '85-89'
          WHEN idade >= 90 AND idade <= 94 THEN '90-94'
          WHEN idade >= 95 AND idade <= 99 THEN '95-99'
          WHEN idade >= 100 AND idade <= 104 THEN '100-104'
          ELSE 'Não informado'
        END AS idade

        , CASE
          WHEN idade <= 4 THEN 0
          WHEN idade >= 5 AND idade <= 9 THEN 1
          WHEN idade >= 10 AND idade <= 14 THEN 2
          WHEN idade >= 15 AND idade <= 19 THEN 3
          WHEN idade >= 20 AND idade <= 24 THEN 4
          WHEN idade >= 25 AND idade <= 29 THEN 5
          WHEN idade >= 30 AND idade <= 34 THEN 6
          WHEN idade >= 35 AND idade <= 39 THEN 7
          WHEN idade >= 40 AND idade <= 44 THEN 8
          WHEN idade >= 45 AND idade <= 49 THEN 9
          WHEN idade >= 50 AND idade <= 54 THEN 10
          WHEN idade >= 55 AND idade <= 59 THEN 11
          WHEN idade >= 60 AND idade <= 64 THEN 12
          WHEN idade >= 65 AND idade <= 69 THEN 13
          WHEN idade >= 70 AND idade <= 74 THEN 14
          WHEN idade >= 75 AND idade <= 79 THEN 15
          WHEN idade >= 80 AND idade <= 84 THEN 16
          WHEN idade >= 85 AND idade <= 89 THEN 17
          WHEN idade >= 90 AND idade <= 94 THEN 18
          WHEN idade >= 95 AND idade <= 99 THEN 19
          WHEN idade >= 100 AND idade <= 104 THEN 20
                ELSE 21
        END AS ordem_faixa
        , CASE
          WHEN identidade_genero LIKE 'homem%' THEN COUNT(id)
        END AS homem
        , CASE
          WHEN identidade_genero LIKE 'mulher%' THEN COUNT(id)
        END AS mulher
      FROM
        (SELECT
          TIMESTAMPDIFF (YEAR,STR_TO_DATE(data_nascimento,'%d/%m/%Y'),CURDATE()) AS idade
          , id
                , identidade_genero
        FROM pacientes)TB
      GROUP BY idade, identidade_genero)TBB
    GROUP BY idade, ordem_faixa
    ORDER BY ordem_faixa;
    ");
    $faixa_etaria_genero = json_encode($faixa_etaria_genero_select);
    return $faixa_etaria_genero;
  }

  public function faixa_etaria_genero_2()
  {
    $faixa_etaria_genero_2_select = DB::select("
    SELECT
      faixa_idade
        , COALESCE(SUM(homem),0) AS homens
        , COALESCE(SUM(mulher),0) AS mulheres
        , COALESCE(SUM(sem_informacao),0) AS sem_informacao
    FROM
      (SELECT
        CASE
          WHEN idade <= 4 THEN '0-4'
          WHEN idade >= 5 AND idade <= 9 THEN '5-9'
          WHEN idade >= 10 AND idade <= 14 THEN '10-14'
          WHEN idade >= 15 AND idade <= 19 THEN '15-19'
          WHEN idade >= 20 AND idade <= 24 THEN '20-24'
          WHEN idade >= 25 AND idade <= 29 THEN '25-29'
          WHEN idade >= 30 AND idade <= 34 THEN '30-34'
          WHEN idade >= 35 AND idade <= 39 THEN '35-39'
          WHEN idade >= 40 AND idade <= 44 THEN '40-44'
          WHEN idade >= 45 AND idade <= 49 THEN '45-49'
          WHEN idade >= 50 AND idade <= 54 THEN '50-54'
          WHEN idade >= 55 AND idade <= 59 THEN '55-59'
          WHEN idade >= 60 AND idade <= 64 THEN '60-64'
          WHEN idade >= 65 AND idade <= 69 THEN '65-69'
          WHEN idade >= 70 AND idade <= 74 THEN '70-74'
          WHEN idade >= 75 AND idade <= 79 THEN '75-79'
          WHEN idade >= 80 AND idade <= 84 THEN '80-84'
          WHEN idade >= 85 AND idade <= 89 THEN '85-89'
          WHEN idade >= 90 AND idade <= 94 THEN '90-94'
          WHEN idade >= 95 AND idade <= 99 THEN '95-99'
          WHEN idade >= 100 AND idade <= 104 THEN '100-104'
          ELSE 'Não informado'
        END AS faixa_idade

        , CASE
          WHEN idade <= 4 THEN 0
          WHEN idade >= 5 AND idade <= 9 THEN 1
          WHEN idade >= 10 AND idade <= 14 THEN 2
          WHEN idade >= 15 AND idade <= 19 THEN 3
          WHEN idade >= 20 AND idade <= 24 THEN 4
          WHEN idade >= 25 AND idade <= 29 THEN 5
          WHEN idade >= 30 AND idade <= 34 THEN 6
          WHEN idade >= 35 AND idade <= 39 THEN 7
          WHEN idade >= 40 AND idade <= 44 THEN 8
          WHEN idade >= 45 AND idade <= 49 THEN 9
          WHEN idade >= 50 AND idade <= 54 THEN 10
          WHEN idade >= 55 AND idade <= 59 THEN 11
          WHEN idade >= 60 AND idade <= 64 THEN 12
          WHEN idade >= 65 AND idade <= 69 THEN 13
          WHEN idade >= 70 AND idade <= 74 THEN 14
          WHEN idade >= 75 AND idade <= 79 THEN 15
          WHEN idade >= 80 AND idade <= 84 THEN 16
          WHEN idade >= 85 AND idade <= 89 THEN 17
          WHEN idade >= 90 AND idade <= 94 THEN 18
          WHEN idade >= 95 AND idade <= 99 THEN 19
          WHEN idade >= 100 AND idade <= 104 THEN 20
                ELSE 21
        END AS ordem_faixa
        , CASE
          WHEN idade LIKE 'homem%' THEN COUNT(id)
        END AS homem
        , CASE
          WHEN idade LIKE 'mulher%' THEN COUNT(id)
        END AS mulher
        , CASE
          WHEN idade IS NULL THEN COUNT(id)
        END AS sem_informacao

      FROM
        (SELECT
          TIMESTAMPDIFF (YEAR,STR_TO_DATE(data_nascimento,'%d/%m/%Y'),CURDATE()) AS idade
          , id
                , idade
        FROM pacientes)TB
      GROUP BY idade, idade)TBB
    GROUP BY faixa_idade, ordem_faixa
    ORDER BY ordem_faixa;
    ");
    return $faixa_etaria_genero_2_select;
  }

  public function faixa_etaria_raca_cor()
  {
    $faixa_etaria_raca_cor_graph = DB::select("
    SELECT
      faixa_idade
        , COALESCE(SUM(sem_informacao),0) AS sem_informacao
        , COALESCE(SUM(preta),0) AS preta
        , COALESCE(SUM(parda),0) AS parda
        , COALESCE(SUM(branca),0) AS branca
        , COALESCE(SUM(amarela),0) AS amarela
        , COALESCE(SUM(indigena),0) AS indigena
    FROM
      (SELECT
        CASE
          WHEN idade <= 4 THEN '0-4'
          WHEN idade >= 5 AND idade <= 9 THEN '5-9'
          WHEN idade >= 10 AND idade <= 14 THEN '10-14'
          WHEN idade >= 15 AND idade <= 19 THEN '15-19'
          WHEN idade >= 20 AND idade <= 24 THEN '20-24'
          WHEN idade >= 25 AND idade <= 29 THEN '25-29'
          WHEN idade >= 30 AND idade <= 34 THEN '30-34'
          WHEN idade >= 35 AND idade <= 39 THEN '35-39'
          WHEN idade >= 40 AND idade <= 44 THEN '40-44'
          WHEN idade >= 45 AND idade <= 49 THEN '45-49'
          WHEN idade >= 50 AND idade <= 54 THEN '50-54'
          WHEN idade >= 55 AND idade <= 59 THEN '55-59'
          WHEN idade >= 60 AND idade <= 64 THEN '60-64'
          WHEN idade >= 65 AND idade <= 69 THEN '65-69'
          WHEN idade >= 70 AND idade <= 74 THEN '70-74'
          WHEN idade >= 75 AND idade <= 79 THEN '75-79'
          WHEN idade >= 80 AND idade <= 84 THEN '80-84'
          WHEN idade >= 85 AND idade <= 89 THEN '85-89'
          WHEN idade >= 90 AND idade <= 94 THEN '90-94'
          WHEN idade >= 95 AND idade <= 99 THEN '95-99'
          WHEN idade >= 100 AND idade <= 104 THEN '100-104'
          ELSE 'Não informado'
        END AS faixa_idade

        , CASE
          WHEN idade <= 4 THEN 0
          WHEN idade >= 5 AND idade <= 9 THEN 1
          WHEN idade >= 10 AND idade <= 14 THEN 2
          WHEN idade >= 15 AND idade <= 19 THEN 3
          WHEN idade >= 20 AND idade <= 24 THEN 4
          WHEN idade >= 25 AND idade <= 29 THEN 5
          WHEN idade >= 30 AND idade <= 34 THEN 6
          WHEN idade >= 35 AND idade <= 39 THEN 7
          WHEN idade >= 40 AND idade <= 44 THEN 8
          WHEN idade >= 45 AND idade <= 49 THEN 9
          WHEN idade >= 50 AND idade <= 54 THEN 10
          WHEN idade >= 55 AND idade <= 59 THEN 11
          WHEN idade >= 60 AND idade <= 64 THEN 12
          WHEN idade >= 65 AND idade <= 69 THEN 13
          WHEN idade >= 70 AND idade <= 74 THEN 14
          WHEN idade >= 75 AND idade <= 79 THEN 15
          WHEN idade >= 80 AND idade <= 84 THEN 16
          WHEN idade >= 85 AND idade <= 89 THEN 17
          WHEN idade >= 90 AND idade <= 94 THEN 18
          WHEN idade >= 95 AND idade <= 99 THEN 19
          WHEN idade >= 100 AND idade <= 104 THEN 20
                ELSE 21
        END AS ordem_faixa
        , CASE WHEN cor_raca IS NULL THEN COUNT(id) END AS sem_informacao
        , CASE WHEN cor_raca = 'Preta' THEN COUNT(id) END AS preta
        , CASE WHEN cor_raca = 'Parda' THEN COUNT(id) END AS parda
        , CASE WHEN cor_raca = 'Branca' THEN COUNT(id) END AS branca
        , CASE WHEN cor_raca = 'Amarela' THEN COUNT(id) END AS amarela
        , CASE WHEN cor_raca = 'Indígena'THEN COUNT(id) END AS indigena
      FROM
        (SELECT
        TIMESTAMPDIFF (YEAR,STR_TO_DATE(data_nascimento,'%d/%m/%Y'),CURDATE()) AS idade
        , id
        , cor_raca
        FROM pacientes)TB
      GROUP BY idade, cor_raca)TBB
    GROUP BY faixa_idade, ordem_faixa
    ORDER BY ordem_faixa;
    ");

    $faixa_etaria_raca_cor_legend = DB::select("
    SELECT CONCAT('*Negras e negros (pretos + pardos): ',GROUP_CONCAT(idade)) AS legenda
    FROM
      (SELECT
        CONCAT(COALESCE(SUM(preta),0) + COALESCE(SUM(parda),0), faixa_idade) idade
      FROM
        (SELECT
        CASE
          WHEN idade <= 4 THEN '0-4'
          WHEN idade >= 5 AND idade <= 9 THEN ' pessoas de 5-9 anos'
          WHEN idade >= 10 AND idade <= 14 THEN ' pessoas de 10-14 anos'
          WHEN idade >= 15 AND idade <= 19 THEN ' pessoas de 15-19 anos'
          WHEN idade >= 20 AND idade <= 24 THEN ' pessoas de 20-24 anos'
          WHEN idade >= 25 AND idade <= 29 THEN ' pessoas de 25-29 anos'
          WHEN idade >= 30 AND idade <= 34 THEN ' pessoas de 30-34 anos'
          WHEN idade >= 35 AND idade <= 39 THEN ' pessoas de 35-39 anos'
          WHEN idade >= 40 AND idade <= 44 THEN ' pessoas de 40-44 anos'
          WHEN idade >= 45 AND idade <= 49 THEN ' pessoas de 45-49 anos'
          WHEN idade >= 50 AND idade <= 54 THEN ' pessoas de 50-54 anos'
          WHEN idade >= 55 AND idade <= 59 THEN ' pessoas de 55-59 anos'
          WHEN idade >= 60 AND idade <= 64 THEN ' pessoas de 60-64 anos'
          WHEN idade >= 65 AND idade <= 69 THEN ' pessoas de 65-69 anos'
          WHEN idade >= 70 AND idade <= 74 THEN ' pessoas de 70-74 anos'
          WHEN idade >= 75 AND idade <= 79 THEN ' pessoas de 75-79 anos'
          WHEN idade >= 80 AND idade <= 84 THEN ' pessoas de 80-84 anos'
          WHEN idade >= 85 AND idade <= 89 THEN ' pessoas de 85-89 anos'
          WHEN idade >= 90 AND idade <= 94 THEN ' pessoas de 90-94 anos'
          WHEN idade >= 95 AND idade <= 99 THEN ' pessoas de 95-99 anos'
          WHEN idade >= 100 AND idade <= 104 THEN ' pessoas de 100-104 anos'
          ELSE ' pessoas sem informação de idade'
        END AS faixa_idade

        , CASE
          WHEN idade <= 4 THEN 0
          WHEN idade >= 5 AND idade <= 9 THEN 1
          WHEN idade >= 10 AND idade <= 14 THEN 2
          WHEN idade >= 15 AND idade <= 19 THEN 3
          WHEN idade >= 20 AND idade <= 24 THEN 4
          WHEN idade >= 25 AND idade <= 29 THEN 5
          WHEN idade >= 30 AND idade <= 34 THEN 6
          WHEN idade >= 35 AND idade <= 39 THEN 7
          WHEN idade >= 40 AND idade <= 44 THEN 8
          WHEN idade >= 45 AND idade <= 49 THEN 9
          WHEN idade >= 50 AND idade <= 54 THEN 10
          WHEN idade >= 55 AND idade <= 59 THEN 11
          WHEN idade >= 60 AND idade <= 64 THEN 12
          WHEN idade >= 65 AND idade <= 69 THEN 13
          WHEN idade >= 70 AND idade <= 74 THEN 14
          WHEN idade >= 75 AND idade <= 79 THEN 15
          WHEN idade >= 80 AND idade <= 84 THEN 16
          WHEN idade >= 85 AND idade <= 89 THEN 17
          WHEN idade >= 90 AND idade <= 94 THEN 18
          WHEN idade >= 95 AND idade <= 99 THEN 19
          WHEN idade >= 100 AND idade <= 104 THEN 20
            ELSE 21
        END AS ordem_faixa
        , CASE WHEN cor_raca IS NULL THEN COUNT(id) END AS sem_informacao
        , CASE WHEN cor_raca = 'Preta' THEN COUNT(id) END AS preta
        , CASE WHEN cor_raca = 'Parda' THEN COUNT(id) END AS parda
        , CASE WHEN cor_raca = 'Branca' THEN COUNT(id) END AS branca
        , CASE WHEN cor_raca = 'Amarela' THEN COUNT(id) END AS amarela
        , CASE WHEN cor_raca = 'Indígena'THEN COUNT(id) END AS indigena
        FROM
        (SELECT
          TIMESTAMPDIFF (YEAR,STR_TO_DATE(data_nascimento,'%d/%m/%Y'),CURDATE()) AS idade
          , id
          , cor_raca
        FROM pacientes)TB
        GROUP BY idade, cor_raca)TBB
      GROUP BY faixa_idade, ordem_faixa
      ORDER BY ordem_faixa)TBBB;
    ");
    return array($faixa_etaria_raca_cor_graph,$faixa_etaria_raca_cor_legend);
  }

}
