<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth'])->group(function () {
  // Route::get('/admin', function() {
  //   return view('sample');
  // })->name('admin');

  Route::get('/admin', function() {
    return view('dashboard');
  })->name('admin');

  Route::get('/admin/agente', 'AgenteController@index')->name('agente');
  Route::get('/admin/agente/add', 'AgenteController@add')->name('agente/add');
  Route::get('/admin/agente/edit/{id}', 'AgenteController@edit')->name('agente/edit');

  Route::get('/admin/medico', 'MedicoController@index')->name('medico');
  Route::get('/admin/medico/add', 'MedicoController@add')->name('medico/add');
  Route::get('/admin/medico/edit/{id}', 'MedicoController@edit')->name('medico/edit');

  Route::get('/admin/psicologo', 'PsicologoController@index')->name('psicologo');
  Route::get('/admin/psicologo/add', 'PsicologoController@add')->name('psicologo/add');
  Route::get('/admin/psicologo/edit/{id}', 'PsicologoController@edit')->name('psicologo/edit');

  Route::get('/admin/paciente', 'PacienteController@index')->name('paciente');
  Route::post('/admin/paciente', 'PacienteController@store')->name('admin.paciente.store');
  Route::get('/admin/paciente/add', 'PacienteController@add')->name('paciente/add');
  Route::get('/admin/paciente/edit/{id}', 'PacienteController@edit')->name('paciente/edit');
  Route::get('/admin/paciente/notify/dismiss/{notification_id}/{paciente_id}', 'NotifyController@dismiss')->name('paciente/notify/dismiss');

  Route::get('/admin/paciente/exportar', 'PacienteController@ExportarExcelPacientes')->name('paciente/exportar');
});
