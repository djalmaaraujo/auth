<?php

/*
    Class: Auth

    Identifies and authenticates users using sessions.

    Dependencies:
        - Model
        - Session
        - Security
*/
class Auth {
    /*
        Constant: SESSION_KEY

        Name of the key to be used to store the user's information in
        the session.
    */
    const SESSION_KEY = 'Auth.user';

    /*
        Variable: $userModel

        Name of the model used to identify users.
    */
    public static $userModel = 'Users';

    /*
        Variable: $fields

        Names of the columns used by the model to identify the user.
        Defaults are: 'username' for the identifier used by the user
        and 'password' for the user's password.

        See Also:
            <Auth::login>
    */
    public static $fields = array(
        'username' => 'username',
        'password' => 'password'
    );

    /*
        Method: login

        Logs a user in using the data provided by the method's parameter.

        Parameters:
            $data - array containing at least the user's identifier and
            its password. The array's keys are the same defined by
            <Auth::$fields>.

        Returns:
            True if the user was logged in. False instead.

        See Also:
            <Auth::$fields>
    */
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

    /*
        Method: logout

        Logs a user out by destroying the session.
    */
    public static function logout() {
        Session::destroy();
    }

    /*
        Method: loggedIn

        Verifies if the current user is logged in.

        Returns:
            True if the user is logged in. False instead.
    */
    public static function loggedIn() {
        return !is_null(Session::read(self::SESSION_KEY));
    }

    /*
        Method: user

        Returns the current logged in user. Be aware that the user info
        may not be accurate, because it reflects the data stored in the
        session, not the database. If you want to update the user's info,
        use <Auth::update>.

        Returns:
            The current logged in user object, null if there is no user
            logged in.

        See Also:
            <Auth::update>
    */
    public static function user() {
        if($this->loggedIn()) {
            Model::load($this->userModel);
            return unserialize(Session::read(self::SESSION_KEY));
        }
    }

    /*
        Method: update

        Updates the user's object stored in the session.

        Params:
            $user - user's object.

        See Also:
            <Auth::user>
    */
    public static function update($user) {
        if(self::loggedIn()) {
            self::writeSession($user);
        }
    }

    /*
        Method: identify

        Query the model to get a user identified by a username/password
        pair. The names of the fields are defined in <Auth::$fields>.

        Params:
            $data - user data. The array keys used are defined in
            <Auth::$fields>.

        Returns:
            The user's object if one is found, null otherwise.

        See Also:
            <Auth::$fields>
    */
    protected static function identify($data) {
        extract(self::$fields);
        return Model::load(self::$userModel)->first(array(
            'conditions' => array(
                $username => $data[$username],
                $password => Security::hash($data[$password])
            )
        ));
    }

    /*
        Method: writeSession

        Writes the serialized user's object to the session.

        Params:
            $user - user's object.
    */
    protected static function writeSession($user) {
        Session::regenerate();
        Session::write(self::SESSION_KEY, serialize($user));
    }
}