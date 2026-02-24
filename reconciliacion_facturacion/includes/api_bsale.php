<?php
// ==========================================================
// BSale API Integration
// /reconciliacion_facturacion/includes/api_bsale.php
// Author: Mauricio Araneda (mAo)
// Date: 2025-12-18
// Encoding: UTF-8 without BOM
// ==========================================================

class BSaleAPI {
    private $token;
    private $apiUrl;
    private $timeout;
    
    public function __construct() {
        $this->token = BSALE_TOKEN;
        $this->apiUrl = BSALE_API_URL;
        $this->timeout = BSALE_API_TIMEOUT;
    }
    
    /**
     * Make API request to BSale
     */
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->apiUrl . $endpoint;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'access_token: ' . $this->token,
            'Content-Type: application/json'
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['error' => $error, 'http_code' => $httpCode];
        }
        
        $result = json_decode($response, true);
        $result['http_code'] = $httpCode;
        
        return $result;
    }
    
    /**
     * Get documents (sales notes) from BSale
     * @param string $startDate Format: YYYY-MM-DD
     * @param string $endDate Format: YYYY-MM-DD
     * @param int $documentTypeId 8 = Nota de Venta
     */
    public function getDocuments($startDate, $endDate, $documentTypeId = 8) {
        $endpoint = "/documents.json?documenttypeid={$documentTypeId}&emissiondaterange=[{$startDate},{$endDate}]&limit=200";
        
        $allDocuments = [];
        $offset = 0;
        $limit = 200;
        
        do {
            $currentEndpoint = $endpoint . "&offset={$offset}";
            $response = $this->makeRequest($currentEndpoint);
            
            if (isset($response['error'])) {
                return $response;
            }
            
            if (isset($response['items']) && is_array($response['items'])) {
                $allDocuments = array_merge($allDocuments, $response['items']);
                $count = count($response['items']);
                $offset += $limit;
            } else {
                break;
            }
            
        } while ($count >= $limit);
        
        return ['documents' => $allDocuments, 'count' => count($allDocuments)];
    }
    
    /**
     * Get specific document details
     */
    public function getDocumentDetails($documentId) {
        $endpoint = "/documents/{$documentId}.json";
        return $this->makeRequest($endpoint);
    }
    
    /**
     * Update document in BSale
     */
    public function updateDocument($documentId, $data) {
        $endpoint = "/documents/{$documentId}.json";
        return $this->makeRequest($endpoint, 'PUT', $data);
    }
    
    /**
     * Get document by number
     */
    public function getDocumentByNumber($number, $documentTypeId = 8) {
        $endpoint = "/documents.json?number={$number}&documenttypeid={$documentTypeId}";
        $response = $this->makeRequest($endpoint);
        
        if (isset($response['items']) && count($response['items']) > 0) {
            return $response['items'][0];
        }
        
        return null;
    }
}

?>
