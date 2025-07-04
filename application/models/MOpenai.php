<?php


class MOpenai extends CI_Model
{
    public function createAssistant($post)
    {
        $openai = $this->db->get_where('tb_openai', ['id_openai' => 1])->row_array();
        $OPENAI_API_KEY = $openai['keys'];

        $payload = [
            'name' => $post['name_assistant'],
            'instructions' => $post['instruction_assistant'],
            'model' => 'gpt-4',
            'tools' => [['type' => 'retrieval']],
        ];

        $ch = curl_init("https://api.openai.com/v1/assistants");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $OPENAI_API_KEY",
                "Content-Type: application/json"
            ]
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $resultArray = json_decode($result, true);

        return $resultArray; // Ambil assistant_id
    }

    public function uploadFile($filePath)
    {
        $openai = $this->db->get_where('tb_openai', ['id_openai' => 1])->row_array();
        $OPENAI_API_KEY = $openai['keys'];

        $ch = curl_init("https://api.openai.com/v1/files");

        $payload = [
            'purpose' => 'assistants',
            'file' => curl_file_create($filePath, 'application/pdf', basename($filePath)),
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $OPENAI_API_KEY"
            ]
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $resultArray = json_decode($result, true);

        return $resultArray; // Ambil file_id
    }

    public function createThread()
    {
        $openai = $this->db->get_where('tb_openai', ['id_openai' => 1])->row_array();
        $OPENAI_API_KEY = $openai['keys'];

        $ch = curl_init("https://api.openai.com/v1/threads");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => '{}',
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $OPENAI_API_KEY",
                "Content-Type: application/json"
            ]
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $resultArray = json_decode($result, true);

        return $resultArray; // Ambil thread_id
    }
}
