<?php

class Auth {
    const SESSION_KEY = 'Auth.user';

    /**
    *  Nome do modelo a ser utilizado para a autenticação.
    */
    public static $userModel = 'Users';

    /**
    *  Nomes dos campos do modelo a serem usados na autenticação.
    */
    public static $fields = array(
        'username' => 'username',
        'password' => 'password'
    );

    public static function login($data) {
        $user = self::identify($data);
        
        if($user) {
            self::writeSession($user);
            return true;
        }
        else {
            return false;
        }
    }

    public static function logout() {
        Session::destroy();
    }

    public static function identify($data) {
        return Model::load($this->userModel)->first(array(
            'conditions' => array(
                self::$fields['username'] => $data[self::$fields['username']],
                self::$fields['password'] => Security::hash($data[self::$fields['password']])
            )
        ));
    }

    public static function loggedIn() {
        return !is_null(Session::read(self::SESSION_KEY));
    }

    /**
    *  Retorna informações do usuário.
    *
    *  @return object Informações do usuário
    */
    public static function user() {
        if($this->loggedIn()) {
            Model::load($this->userModel);
            return unserialize(Session::read(self::SESSION_KEY));
        }
    }

    /**
    *  Atualiza informações na sessão do usuário
    *
    *  @param object $user Objeto do usuário
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