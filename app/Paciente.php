<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
  protected $fillable = [
    'user_id',
    'agente_id',
    'medico_id',
    'psicologo_id',
    'situacao',
    'data_nascimento',
    'cor_raca',
    'endereco_cep',
    'endereco_rua',
    'endereco_numero',
    'endereco_bairro',
    'endereco_cidade',
    'endereco_uf',
    'ponto_referencia',
    'endereco_complemento',
    'fone_fixo',
    'fone_celular',
    'numero_pessoas_residencia',
    'responsavel_residencia',
    'renda_residencia',
    'doenca_cronica',
    'outras_doencas',
    'remedios_consumidos',
    'acompanhamento_medico',
    'isolamento_residencial',
    'alimentacao_disponivel',
    'auxilio_terceiros',
    'tarefas_autocuidado',
    'sintomas_iniciais',
    'data_teste_confirmatorio',
    'teste_utilizado',
    'data_inicio_sintoma',
    'data_inicio_monitoramento',
    'data_finalizacao_caso',
    'name_social',
    'identidade_genero',
    'orientacao_sexual',
    'auxilio_emergencial',
    'descreve_doencas',
    'tuberculose',
    'tabagista',
    'cronico_alcool',
    'outras_drogas',
    'gestante',
    'amamenta',
    'gestacao_alto_risco',
    'pos_parto',
    'data_parto',
    'data_ultima_mestrucao',
    'trimestre_gestacao',
    'motivo_risco_gravidez',
    'data_ultima_consulta',
    'sistema_saude',
    'acompanhamento_ubs',
    'resultado_teste',
    'articuladora_responsavel',
    'valor_familia',
    'outras_informacao',
  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function agente()
  {
    return $this->belongsTo('App\Agente');
  }

  public function medico()
  {
    return $this->belongsTo('App\Medico');
  }

  public function psicologo()
  {
    return $this->belongsTo('App\Psicologo');
  }

  public function articuladora()
  {
    return $this->belongsTo('App\Articuladora');
  }

  public function sintomas()
  {
    return $this->hasMany('App\Sintoma');
  }

  public function tipos_ajuda()
  {
    return $this->hasMany('App\AjudaTipo');
  }

  public function estado_emocional()
  {
    return $this->hasOne('App\EstadoEmocional');
  }

  public function observacao()
  {
    return $this->hasOne('App\Observacao');
  }

  public function doencas_cronicas()
  {
    return $this->hasMany('App\DoencaCronica');
  }

  public function items()
  {
    return $this->hasOne('App\Item');
  }

  public function dados()
  {
    return $this->hasMany('App\EvolucaoSintoma');
  }

  public function insumos_oferecidos()
  {
    return $this->hasMany('App\InsumosOferecido');
  }

  public function quadro_atual()
  {
    return $this->hasMany('App\QuadroAtual');
  }

  public function saude_mental()
  {
    return $this->hasMany('App\SaudeMental');
  }

  public function servico_internacao()
  {
    return $this->hasMany('App\ServicoInternacao');
  }
}
