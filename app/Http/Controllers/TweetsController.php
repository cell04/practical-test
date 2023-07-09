<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tweets\StoreTweetFormRequest;
use App\Http\Requests\Tweets\StoreTweetImageFormRequest;
use App\Http\Requests\Tweets\UpdateTweetFormRequest;
use App\Http\Resources\TweetResource;
use App\Repositories\TweetRepository;
use Illuminate\Http\Request;

class TweetsController extends Controller
{
    private $tweetRepository;

    public function __construct(TweetRepository $tweetRepository)
    {
        $this->tweetRepository = $tweetRepository;
    }


    /**
    *   @OA\Get(
    *       path="/tweets",
    *       summary="Tweets Index",
    *       description="List of tweets",
    *       operationId="indexTweet",
    *       tags={"Tweets"},
    *       @OA\Response(
    *           response=200,
    *           description="Success"
    *       ),
    *       @OA\Response(
    *           response=401,
    *           description="Returns when user is not authenticated",
    *           @OA\JsonContent(
    *               @OA\Property(property="message", type="string", example="Not authorized"),
    *           )
    *       )
    *   )
    */
    public function index()
    {
        return TweetResource::collection(
            $this->tweetRepository->paginateWithFilters(
                request(),
                request()->per_page,
                request()->order_by
            )
        );
    }


    /**
    *   @OA\Post(
    *       path="/tweets",
    *       summary="Tweets Store",
    *       description="Create new tweet",
    *       operationId="storeTweet",
    *       tags={"Tweets"},
    *       @OA\RequestBody(
    *           required=true,
    *           description="Pass user credentials",
    *           @OA\MediaType(
    *               mediaType="multipart/form-data",
    *               @OA\Schema(
    *                   type="object", 
    *                   required={"tweet", "image"},
    *                   @OA\Property(
    *                       property="image",
    *                       type="array",
    *                       @OA\Items(
    *                           type="string",
    *                           format="binary",
    *                       ),
    *                   ),
    *                   @OA\Property(property="tweet", type="string", example="Sample Tweet"),
    *               ),
    *           ),
    *       ),
    *       @OA\Response(
    *           response=201,
    *           description="Success",
    *       ),
    *       @OA\Response(
    *           response=422,
    *           description="Unauthorized",
    *       ),
    *   )
    */
    public function store(StoreTweetFormRequest $request)
    {
        return response()->json([
            'message' => 'Resource successfully stored',
            'tweet' => $this->tweetRepository->store($request)
        ], 201);
    }

    /**
    *   @OA\Get(
    *   path="/tweets/{id}",
    *   description="Show detail of tweet",
    *       summary="Tweets Show",
    *   operationId="showTweet",
    *   tags={"Tweets"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\Response(
    *           response=200,
    *           description="Success",
    *       ),
    *   )
    */
    public function show($id)
    {
        return response()->json([
            'message' => 'Resource successfully retrieve',
            'tweet' => $this->tweetRepository->findOrFail($id)
        ], 200);
    }

    /**
    *   @OA\PATCH(
    *       path="/tweets/{id}",
    *       summary="Tweets Update",
    *       description="Update tweet",
    *       operationId="updateTweet",
    *       tags={"Tweets"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\RequestBody(
    *           required=true,
    *           description="Pass user credentials",
    *           @OA\JsonContent(
    *               required={"tweet"},
    *               @OA\Property(property="tweet", type="string", example="Sample Tweet"),
    *           ),
    *       ),
    *       @OA\Response(
    *           response=202,
    *           description="Success",
    *       ),
    *       @OA\Response(
    *           response=422,
    *           description="Unauthorized",
    *       ),
    *   )
    */
    public function update(UpdateTweetFormRequest $request, $id)
    {
        $this->tweetRepository->update($request, $id);

        return response()->json([
            'message' => 'Resource successfully updated'
        ], 202);
    }

    /**
    *   @OA\Delete(
    *   path="/tweets/{id}",
    *   description="Tweets delete",
    *       summary="Tweets Delete",
    *   operationId="deleteTweet",
    *   tags={"Tweets"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\Response(
    *           response=202,
    *           description="Success",
    *       ),
    *   )
    */  
    public function destroy($id)
    {
        $this->tweetRepository->destroy($id);

        return response()->json([
            'message' => 'Resource successfully deleted'
        ], 202);
    }


    /**
    *   @OA\Post(
    *       path="/tweets/{id}/images",
    *       summary="Tweets Store Image",
    *       description="Create new tweet image",
    *       operationId="storeTweetImage",
    *       tags={"Tweets"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\RequestBody(
    *           required=true,
    *           description="Pass user credentials",
    *           @OA\MediaType(
    *               mediaType="multipart/form-data",
    *               @OA\Schema(
    *                   type="object", 
    *                   @OA\Property(
    *                       property="image",
    *                       type="array",
    *                       @OA\Items(
    *                           type="string",
    *                           format="binary",
    *                       ),
    *                   ),
    *               ),
    *           ),
    *       ),
    *       @OA\Response(
    *           response=201,
    *           description="Success",
    *       ),
    *       @OA\Response(
    *           response=422,
    *           description="Unauthorized",
    *       ),
    *   )
    */
    public function storeImage(StoreTweetImageFormRequest $request, $id)
    {
        return response()->json([
            'message' => 'Resource successfully stored',
            'tweet' => $this->tweetRepository->storeImage($request, $id)
        ], 201);
    }

    /**
    *   @OA\Delete(
    *       path="/tweets/{id}/images/{image}",
    *       summary="Tweets Delete Image",
    *       description="Delete new tweet image",
    *       operationId="deleteTweetImage",
    *       tags={"Tweets"},
    *       @OA\Parameter(
    *           in="path",
    *           name="id",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\Parameter(
    *           in="path",
    *           name="image",
    *           required=true,
    *           @OA\Schema(type="number"),
    *           @OA\Examples(example="id", value="1", summary="An ID value."),
    *       ),
    *       @OA\Response(
    *           response=202,
    *           description="Success",
    *       ),
    *       @OA\Response(
    *           response=402,
    *           description="Not Found",
    *       ),
    *   )
    */
    public function deleteImage($id, $image)
    {
        return response()->json([
            'message' => 'Resource successfully deleted',
            'tweet' => $this->tweetRepository->deleteImage($id, $image)
        ], 202);
    }

    /**
    *   @OA\Get(
    *       path="/tweets/users-followed-tweets",
    *       description="Show followed users tweet",
    *       summary="List Followed Users Tweets",
    *       operationId="showFollowedUsersTweets",
    *       tags={"Tweets"},
    *       @OA\Response(
    *           response=200,
    *           description="Success",
    *       ),
    *   )
    */
    public function followedTweets()
    {
        return TweetResource::collection(
            $this->tweetRepository->followedTweets(
                request(),
                request()->per_page,
                request()->order_by
            )
        );
    }
}
