<?php

namespace App\Features\AI\Services;

use App\Models\DetectedIngredient;
use App\Models\IngredientImage;

class ImageDetectionService
{
    /**
     * Upload ingredient image for detection.
     */
    public function uploadImage(int $userId, string $imageUrl): IngredientImage
    {
        return IngredientImage::create([
            'user_id' => $userId,
            'image_url' => $imageUrl,
            'status' => 'pending',
        ]);
    }

    /**
     * Process image detection (placeholder for AI integration).
     */
    public function processDetection(int $imageId): void
    {
        $image = IngredientImage::find($imageId);

        if (! $image) {
            return;
        }

        // TODO: Integrate with AI service (Google Vision, AWS Rekognition, etc.)
        // For now, mark as processed
        $image->update(['status' => 'processed']);

        // Placeholder: Store mock detected ingredients
        // In production, this will come from AI service response
    }

    /**
     * Store detected ingredients.
     */
    public function storeDetectedIngredients(int $imageId, array $detections): void
    {
        foreach ($detections as $detection) {
            DetectedIngredient::create([
                'ingredient_image_id' => $imageId,
                'ingredient_id' => $detection['ingredient_id'] ?? null,
                'detected_name' => $detection['name'],
                'confidence' => $detection['confidence'],
            ]);
        }
    }

    /**
     * Get detection results.
     */
    public function getDetectionResults(int $imageId)
    {
        return DetectedIngredient::where('ingredient_image_id', $imageId)
            ->with('ingredient')
            ->orderBy('confidence', 'desc')
            ->get();
    }

    /**
     * Get user's detection history.
     */
    public function getUserHistory(int $userId, int $perPage = 15)
    {
        return IngredientImage::where('user_id', $userId)
            ->with('detectedIngredients.ingredient')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
