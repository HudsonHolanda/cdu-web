<?php
require_once __DIR__ . '/Conection.php';
class Agent
{
    private object $connection;

    private int $id_user;
    private string $name;
    private string $email;
    private string $password;
    private string $cpf;


    public function __construct()
    {
        $this->connection = new Connection();
    }
    public function __GET($attribute)
    {
        if (!property_exists($this, $attribute)) {
            throw new Exception("Atributte $attribute not exists in  the class  Agent");
        }
        return $this->$attribute;
    }
    public function __SET($attribute, $value)
    {
        if (!property_exists($this, $attribute)) {
            throw new Exception("Atributte $attribute not exists in  the class  Agent");
        }
        $this->$attribute = $value;
    }
    public function InsertAgent()
    {
      if($this->email == null || $this->password == null) return null;
      try{

        $sql = "INSERT INTO users (name, email, password, cpf, role)
        VALUES (UPPER(TRIM(:name)), TRIM(:email), TRIM(:password), TRIM(:cpf),
        :role) 
        ON CONFLICT (email) DO UPDATE SET name = EXCLUDED.name, 
        password = EXCLUDED.password, cpf = EXCLUDED.cpf RETURNING id_user";
        $stmt = $this->connection->conect()->prepare($sql);
        $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $this->password, PDO::PARAM_STR);
        $stmt->bindParam(':cpf', $this->cpf, PDO::PARAM_STR);
        $stmt->bindValue(':role', 1, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
        
      }catch(Exception $e){
        throw new Exception("Error in InsertAgent: " . $e->getMessage());
      }
       // Implementation for Get or Insert logic
    }
}