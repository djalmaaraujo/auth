<?php

class Auth {
    const SESSION_KEY = 'Auth.user';

    /**
    *  Nome do modelo a ser utilizado para a autenticação.
    */
    public $userModel = 'Users';

    /**
    *  Nomes dos campos do modelo a serem usados na autenticação.
    */
    public $fields = array(
        'id' => 'id',
        'username' => 'username',
        'password' => 'password'
    );

    public static function login($user) {
        self::writeSession($user);
    }

    public static function logout() {
        Session::destroy();
    }

    public static function identify($data) {
        return Model::load($this->userModel)->first(array(
            'conditions' => array(
                $this->fields['username'] => $data['username'],
                $this->fields['password'] => Security::hash($data['password'])
            )
        ));
    }

    public static function loggedIn() {
        return !is_null(Session::read(self::SESSION_KEY));
    }

    /**
    *  Retorna informações do usuário.
    *
    *  @param string $field Campo a ser retornado
    *  @return mixed Campo escolhido ou todas as informações do usuário
    */
    public static function user($field = null) {
        if($this->loggedIn()) {
            Model::load($this->userModel);
            $user = unserialize(Session::read(self::SESSION_KEY));
            return (!is_null($field)) ? $user->{$field} : $user;
        }
    }

    /**
    *  Atualiza informações na sessão do usuário
    *
    *  @param array $user Objecto do usuário
    */
    public static function update($user) {
        if(self::loggedIn()) {
            self::writeSession($user);
        }
    }

    protected static function writeSession($user) {
        Session::regenerate();
        Session::write(self::SESSION_KEY, serialize($user));
    }
}