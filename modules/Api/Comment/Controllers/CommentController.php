<?php

namespace Movie\Api\Comment\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Movie\Api\Comment\Services\CommentService;
use Movie\Api\Comment\Resources\CommentResource;

class CommentController extends Controller
{

    /**
     * @var CommentService
     */
    protected $CommentService;

    /**
     * @var int
     */
    const SUCCESSCODE = 1;

    /**
     * @var array
     */
    const ATTRIBUTES = [
        'email',
        'movie_id',
        'comment'
    ];

    /**
     * Comment constructor
     *
     * @param CommentService
     */
    public function __construct(CommentService $CommentService)
    {
        $this->CommentService = $CommentService;
    }


    /**
     * Retrieve a list of Comments.
     *
     * Routing: /Comments
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $inputs   = $request->only('limit', 'offset');

        $validator = Validator::make($inputs, [
            'limit'    => 'integer',
            'offset' => 'integer',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error.', $validator->errors(), 400);
        }
        $inputs = $this->CommentService->setDefaultLimitOffset($inputs);
        $Comments = $this->CommentService->getComments($inputs);

        return sendResponse(CommentResource::collection($Comments), 200, $inputs['limit'], $inputs['offset']);
    }

    /**
     * Create Comment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $inputs = array_filter($request->only(self::ATTRIBUTES), function ($v) {
            return $v !== null;
        });

        $validator = Validator::make($inputs, [
            'email'    => 'required|email',
            'movie_id'  => 'required|integer',
            'comment' => 'required|max:225|min:0',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error.', $validator->errors(), 400);
        }

        $created = $this->CommentService->create($inputs);

        return sendResponse(new CommentResource($created), 201);
    }

    /**
     * Show Comment
     *
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        $Comment = $this->CommentService->show($id);

        if (is_null($Comment)) {
            $message = new MessageBag();
            $message->add('resource_not_found_exception ', 'The resource of the given ID was not found.');
            return sendError('Not Found', $message, 404);
        }

        return sendResponse(new CommentResource($Comment), 200);
    }

    /**
     * Update Comment
     *
     * @param int $id
     * @param Request $request
     */
    public function update(int $id, Request $request)
    {
        $Comment = $this->CommentService->show($id);

        if (is_null($Comment)) {
            $message = new MessageBag();
            $message->add('resource_not_found_exception ', 'The updating resource of the given ID was not found.');
            return sendError('Not Found', $message, 404);
        }

        $inputs = array_filter($request->only(self::ATTRIBUTES), function ($v) {
            return $v !== null;
        });

        $validator = Validator::make($inputs, [
            'email'    => 'email|max:30|min:0',
            'comment' => 'max:225|min:0',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error.', $validator->errors(), 400);
        }
        $Comment->fill($inputs);
        $updated = $this->CommentService->update($Comment);

        return ($updated === true) ? sendResponse(new CommentResource($Comment), 200) : $updated;
    }

    /**
     * Delete Comment
     *
     * @param $id
     */
    public function destroy(int $id)
    {
        $Comment = $this->CommentService->show($id);

        if (is_null($Comment)) {
            $message = new MessageBag();
            $message->add('resource_not_found_exception ', 'The deleting resource of the given ID was not found.');
            return sendError('Not Found', $message, 404);
        }

        $this->CommentService->delete($Comment);

        return sendResponse(['deleted' => 1], 200);
    }

    /**
     * Upload Image
     *
     * @param int $id
     */
    public function uploadImage(int $id, Request $request)
    {

        $Comment = $this->CommentService->show($id);

        if (is_null($Comment)) {
            $message = new MessageBag();
            $message->add('resource_not_found_exception ', 'The updating resource of the given ID was not found.');
            return sendError('Not Found', $message, 404);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            $message = new MessageBag();
            $message->add('image_path ', 'Uploading image was failed');
            return sendError('upload error', $message, 400);
        }
        $image = $request->file('image');

        $this->CommentService->upload($id, $Comment, $image);

        return sendResponse(['uploaded' => 1], 201);
    }
}
