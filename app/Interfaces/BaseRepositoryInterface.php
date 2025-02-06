<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface BaseRepositoryInterface
{
    /**
     * Crea un nuevo registro en la base de datos.
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes);

    /**
     * Actualiza un registro existente en la base de datos.
     *
     * @param array $attributes
     * @param int $id
     * @return bool
     */
    public function update(array $attributes, int $id): bool;

    /**
     * Obtiene todos los registros de la base de datos.
     *
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function all($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc');

    /**
     * Encuentra un registro por su ID.
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id);

    /**
     * Encuentra un registro por su ID o lanza una excepción si no lo encuentra.
     *
     * @param int $id
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOneOrFail(int $id);

    /**
     * Encuentra registros que coincidan con los criterios especificados.
     *
     * @param array $data
     * @return mixed
     */
    public function findBy(array $data);

    /**
     * Encuentra el primer registro que coincida con los criterios especificados.
     *
     * @param array $data
     * @return Model|null
     */
    public function findOneBy(array $data);

    /**
     * Encuentra el primer registro que coincida con los criterios especificados o lanza una excepción si no lo encuentra.
     *
     * @param array $data
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOneByOrFail(array $data);

    /**
     * Pagina un array de resultados.
     *
     * @param array $data
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateArrayResults(array $data, int $perPage = 50): LengthAwarePaginator;

    /**
     * Elimina un registro por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
