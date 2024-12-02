<?php
namespace app\controllers;

class MainController extends Controller {
    /**
     * Display the homepage.
     */
    public function homepage() {
        $this->returnView('main/homepage.html', true);
    }

    /**
     * ChatGPT recommendation based on user input.
     */
    public function getChatGPTRecommendation() {
        $apiKey = OPENAI_API_KEY;
        $url = "https://api.openai.com/v1/chat/completions";
    
        $postData = json_decode(file_get_contents("php://input"), true);
        $question = $postData['question'] ?? 'What are some cryptocurrency investment tips?';
    
        $headers = [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json",
        ];
    
        $payload = json_encode([
            "model" => "gpt-3.5-turbo",
            "messages" => [
                ["role" => "system", "content" => "You are a cryptocurrency advisor."],
                ["role" => "user", "content" => $question],
            ],
            "max_tokens" => 150,
        ]);
    
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $payload,
        ]);
    
        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            $this->returnJSON(['status' => 'error', 'message' => 'Error communicating with OpenAI: ' . curl_error($ch)]);
            curl_close($ch);
            return;
        }
    
        curl_close($ch);
    
        $responseData = json_decode($response, true);
        if (isset($responseData['error'])) {
            // Handle errors from OpenAI
            $this->returnJSON([
                'status' => 'error',
                'message' => $responseData['error']['message'] ?? 'Unknown error occurred.',
            ]);
            return;
        }
    
        $recommendation = $responseData['choices'][0]['message']['content'] ?? 'No recommendation available.';
        $this->returnJSON(['recommendation' => $recommendation]);
    }    

    /**
     * Live cryptocurrency prices using the CoinMarketCap API.
     */
    public function getCryptoPrices() {
        $apiKey = CMC_API_KEY;

        if (!$apiKey) {
            $this->returnJSON([
                'status' => 'error',
                'message' => 'API key not found. Please check your environment configuration.'
            ]);
            return;
        }

        $url = $this->buildCMCRequestUrl('BTC,ETH,ADA,SOL,BNB', 'USD');

        $headers = [
            'Accepts: application/json',
            "X-CMC_PRO_API_KEY: $apiKey",
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->returnJSON(['status' => 'error', 'message' => 'Error fetching cryptocurrency prices: ' . curl_error($ch)]);
            curl_close($ch);
            return;
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['status']['error_message'])) {
            $this->returnJSON(['status' => 'error', 'message' => $responseData['status']['error_message']]);
            return;
        }

        $this->returnJSON(['status' => 'success', 'data' => $responseData['data'] ?? []]);
    }

    private function buildCMCRequestUrl($symbols, $convert) {
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
        $parameters = ['symbol' => $symbols, 'convert' => $convert];
        return "{$url}?" . http_build_query($parameters);
    }
}
