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
    
        // Check if the API key is available
        if (!$apiKey) {
            $this->returnJSON([
                'status' => 'error',
                'message' => 'API key is missing.'
            ]);
            return;
        }
    
        $postData = json_decode(file_get_contents("php://input"), true);
        $question = $postData['question'] ?? 'What are some cryptocurrency investment tips?';
    
        // Echo 
        echo 'Question: ' . $question;  
    
        $payload = json_encode([
            "model" => "gpt-4o-mini",
            "messages" => [
                ["role" => "system", "content" => "You are a cryptocurrency advisor. Answer any questions about cryptocurrency investments, portfolio management, and market trends."],
                ["role" => "user", "content" => $question],
            ],
            "max_tokens" => 150,
        ]);
    
        $headers = [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json",
        ];

        $ch = curl_init("https://api.openai.com/v1/chat/completions");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $payload,
        ]);
    
 
        $response = curl_exec($ch);
    
        // Check for cURL errors
        if (curl_errno($ch)) {
            $this->returnJSON(['status' => 'error', 'message' => 'Error communicating with OpenAI: ' . curl_error($ch)]);
            curl_close($ch);
            return;
        }
    
        curl_close($ch);
    
        echo "Raw Response:\n";
        echo $response;
    
        // Decode the response 
        $responseData = json_decode($response, true);
    
        // Get the recommendation
        $recommendation = $responseData['choices'][0]['message']['content'] ?? 'No recommendation available.';
    
        // Return the recommendation
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
