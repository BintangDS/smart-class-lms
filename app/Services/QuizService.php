<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\User;

class QuizService
{
    /**
     * Submit quiz answers, calculate score automatically, and store attempt.
     */
    public function submitAnswers(User $user, Quiz $quiz, array $answers): QuizAttempt
    {
        $quiz->load('questions.options');
        $questions = $quiz->questions;
        $totalQuestions = $questions->count();

        if ($totalQuestions === 0) {
            return QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'score' => 0,
                'submitted_at' => now(),
            ]);
        }

        // Key correct options by question_id
        $correctOptions = QuizOption::whereIn('question_id', $questions->pluck('id'))
            ->where('is_correct', true)
            ->get()
            ->keyBy('question_id');

        // Map student answers by question_id for quick lookup
        $studentAnswers = collect($answers)->keyBy('question_id');

        $correctCount = 0;

        foreach ($questions as $question) {
            $submittedAnswer = $studentAnswers->get($question->id);
            $correctOption = $correctOptions->get($question->id);

            if ($submittedAnswer && $correctOption && (int)$submittedAnswer['option_id'] === (int)$correctOption->id) {
                $correctCount++;
            }
        }

        $score = (int) round(($correctCount / $totalQuestions) * 100);

        return QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'score' => $score,
            'submitted_at' => now(),
        ]);
    }
}
