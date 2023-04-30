<?php
 
function apiResponse($message, $data, $statusCode){
    if ($statusCode >= 200 && $statusCode <= 300) {
        return response()->json([
            'meta' => [
                'status' => true,
                'message' => $message
            ],
            'body' => $data
        ], $statusCode);
    } else if ($statusCode >= 400 && $statusCode < 500) {

        return response()->json([
            'meta' => [
                'status' => false,
                'message' => $message
            ],
            'body' => $data
        ], $statusCode);
    } else {
        return response()->json([
            'meta' => [
                'status' => false,
                'message' => $message
            ],
            'body' => $data
        ], $statusCode);
    }
}