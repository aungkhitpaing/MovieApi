<?php

namespace Movie\Api\Comment\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\MessageBag;
use Movie\Api\Comment\Models\Comment;
use Movie\Api\Movie\Models\Movie;

class CommentService
{
    /**
     * @var Comment
     */
    protected $model;

    /**
     * @var Movie
     */
    protected $movieModel;

    /**
     * @var limit
     */
    protected int $limit = 30;

    /**
     * @var offset
     */
    protected int $offset = 0;


    public function __construct()
    {
        $this->model = new Comment();
        $this->movieModel = new Movie();
    }

    /**
     * Retrieve get all Comments
     *
     * @param array $inputs
     */
    public function getComments(array $inputs = []): Collection
    {
        $Comments = $this->model->skip($inputs['offset'])->take($inputs['limit'])->orderBy('created_at', 'DESC')->get();
        return $Comments;
    }

    /**
     * Create Comment
     *
     * @param array $inputs
     */
    public function create(array $inputs = [])
    {
        try {

            if(isset($inputs['movie_id'])) {

                $movie  = $this->movieModel->find($inputs['movie_id']);
                if (is_null($movie)) {
                    $message = new MessageBag();
                    $message->add('resource_not_found_exception ', 'The updating resource of the given ID was not found.');
                    return sendError('Not Found', $message, 404);
                }
            }

            return  $this->model->create($inputs);

        } catch (\Exception $e) {

            $message = new MessageBag();
            $message->add('Db ', 'Fail to insert data into table');
            return sendError('Database', $message, 400);
        }
    }

    /**
     * Show Comment by Id
     *
     * @param int $id
     */
    public function show(int $id)
    {
        $Comment = $this->model->find($id);
        return $Comment;
    }

    /**
     * update Comment by Id
     *
     * @param Comment
     */
    public function update(Comment $Comment)
    {
        try {
            return $Comment->save();
        } catch (\Exception $e) {
            $message = new MessageBag();
            $message->add('UpdateDBErrorException ', 'Fail to update data into table');
            return sendError('UpdateDBErrorException', $message, 400);
        }
    }

    public function delete(Comment $Comment)
    {
        return $Comment->delete();
    }

    /**
     * set Default limit and offset
     *
     * @param array $input
     * @return array
     */
    public function setDefaultLimitOffset(array $inputs): array
    {
        return [
            'limit' => isset($inputs['limit']) ? (int)$inputs['limit'] : $this->limit,
            'offset' => isset($inputs['offset']) ? (int)$inputs['offset'] : $this->offset
        ];
    }

}
