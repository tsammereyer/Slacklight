<?php 
namespace Data;

use Slacklight\Channel;
use Slacklight\Message;
use Slacklight\User;
use Slacklight\Topic;

class DataManager {

    private static $__connection; 

    private static function getConnection() {
        if (!isset(self::$__connection)) {
            self::$__connection = new \PDO('mysql:host=localhost;dbname=fh_2018_scm4_S1610307035;charset=utf8', 'root', '');
        }
        return self::$__connection;

    }

    private static function query ($connection, $query, $parameters = array()) {
        $statement = $connection->prepare($query);
        $i = 1;
        foreach ($parameters as $param) {
            if (is_int($param)) {
                $statement->bindValue($i, $param,\PDO::PARAM_INT); 
            }
            if (is_string($param)) {
                $statement->bindValue($i, $param,\PDO::PARAM_STR); 
            }
            $i++;
        }

        $statement->execute();
        // var_dump($statement->debugDumpParams());
        return $statement;
    }

    private static function fetchObject($cursor) {
        return $cursor->fetchObject();
    }

    private static function lastInsertId($connection) {
		return $connection->lastInsertId();
	}

    private static function closeConnection() {
        self::$__connection = null;
    }


    public static function getChannels() : array {
        $channels = array();
        $con = self::getConnection();
        $res = self::query($con, "
            SELECT id, name 
            FROM channel
        ");

        while ($cat = self::fetchObject($res)) {
            $channels[] = new Channel($cat->id, $cat->name);
        }
        self::closeConnection();

        return $channels;

    }

    public static function getChannelsByUserId(int $userId) : array {
        $channels = array();
        $con = self::getConnection();
        $res = self::query($con, "
            SELECT channel.id, name 
            FROM channel
            JOIN channel_user_reference ON (channel.id = channel_id)
            WHERE user_id = ?
        ", array($userId));

        while ($cat = self::fetchObject($res)) {
            $channels[] = new Channel($cat->id, $cat->name);
        }
        self::closeConnection();

        return $channels;

    }

    public static function getMessagesByChannelId(int $channelId) : array {
      $messages = array();
      $con = self::getConnection();
      $res = self::query($con, "
        SELECT message.id, user_id ,channel_id, name, content, created, seen, favourite
        FROM message 
        JOIN users ON (users.id = user_id)
        WHERE deleted = 0
        AND channel_id = ?;
        ", array($channelId));
      while ($message = self::fetchObject($res)) {
            $messages[] = new Message($message->id, $message->user_id, $message->channel_id, $message->name, $message->content, $message->created, $message->seen, $message->favourite);
      }
      self::closeConnection();
      return $messages; 
    }

    public static function getChannelByChannelId(int $channelId) : string {
        $channel = null;

        $con = self::getConnection();
        $res = self::query($con, "
            SELECT id, name 
            FROM channel
            WHERE id = ?;
        ", array($channelId));

        if ($temp = self::fetchObject($res)) {
            $channel = $temp->name;
        }
        return $channel;
    }



    public static function getTopicsByChannelId(int $channelId) : array {
        $topics = array();
        $con = self::getConnection();
        $res = self::query($con, "
          SELECT id, channel_id, name
          FROM topic 
          WHERE channel_id = ?;
          ", array($channelId));
        while ($topic = self::fetchObject($res)) {
              $topics[] = new Topic($topic->id, $topic->channel_id, $topic->name);
        }
        self::closeConnection();
        return $topics; 
      }

    public static function getUserByUserName (string $userName) {
       $user = null;
       $con = self::getConnection();
       $res = self::query($con, "
         SELECT id, name, password 
         FROM users
         WHERE name = ?
       ", array($userName));
       if ($u = self::fetchObject($res)) {
           $user = new User($u->id, $u->name, $u->password);
       }
       self::closeConnection();
       return $user;
    }

    public static function getUserById (int $userId) {
        $user = null;
        $con = self::getConnection();
        $res = self::query($con, "
          SELECT id, name, password 
          FROM users
          WHERE id = ?
        ", array($userId));
        if ($u = self::fetchObject($res)) {
            $user = new User($u->id, $u->name, $u->password);
        }
        self::closeConnection();
        return $user;
    }

    public static function createUser(string $userName, string $passwordHash) : int {
        
        $con = self::getConnection();
        $con->beginTransaction();
        try {
            self::query($con,"
                INSERT INTO users (
                    name,
                    password
                ) VALUES (
                    ?, ?
                );
            
                ", [
                    $userName,
                    $passwordHash
                ]);
            $userId = self::lastInsertId($con);
            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            $userId = null;
        }
        self::closeConnection($con);
	    return $userId;
    }

    //create message
    //INSERT INTO `message` (`id`, `user_id`, `channel_id`, `content`, `created`, `deleted`) 
    //VALUES ('4', '1', '1', 'asdasdasdasdasdasd', CURRENT_TIMESTAMP, '0');
    public static function sendMessage (int $userId, int $channelId, string $content) : int {
        
        $con = self::getConnection();
  
        $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        $con->beginTransaction();
        try {
          self::query($con, "
              INSERT INTO message (
                  user_id,
                  channel_id,
                  content,
                  created,
                  deleted,
                  seen,
                  favourite
              ) VALUES (
                  ?,
                  ?,
                  ?,
                  CURRENT_TIMESTAMP,
                  0,
                  0,
                  0
              );", array($userId, $channelId, $content));
  
              $messageId = $con->lastInsertId();
              $con->commit();
          
        }
        catch (Exception $e) {
            $con->rollBack();
            $messageId = null;
        }
        self::closeConnection();
        return $messageId;
      }

      public static function deleteMessage (int $messageId) : int {
          //var_dump($messageId);
          //die();
        
        $con = self::getConnection();
  
        $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        $con->beginTransaction();
        try {
          self::query($con, "
          UPDATE message
            SET deleted = 1
            WHERE id = ?", array($messageId));
  
            $con->commit();
            $result = true;
        }
        catch (Exception $e) {
            $con->rollBack();
            $result = false;
        }
        self::closeConnection();
        return $result;
      }

    public static function starMessage (int $messageId) : int {
        //var_dump($messageId);
        //die();
      
      $con = self::getConnection();

      $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      
      $con->beginTransaction();
      try {
        self::query($con, "
        UPDATE message
          SET favourite = 1
          WHERE id = ?", array($messageId));

          $con->commit();
          $result = true;
      }
      catch (Exception $e) {
          $con->rollBack();
          $result = false;
      }
      self::closeConnection();
      return $result;
    }

    public static function unStarMessage (int $messageId) : int {
        //var_dump($messageId);
        //die();
      
      $con = self::getConnection();

      $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      
      $con->beginTransaction();
      try {
        self::query($con, "
        UPDATE message
          SET favourite = 0
          WHERE id = ?", array($messageId));

          $con->commit();
          $result = true;
      }
      catch (Exception $e) {
          $con->rollBack();
          $result = false;
      }
      self::closeConnection();
      return $result;
    }

    public static function createOrder (int $userId, array $bookIds, string $nameOnCard, string $cardNumber) : int {
      $con = self::getConnection();

      $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      
      $con->beginTransaction();
      try {
        self::query($con, "
            INSERT INTO orders (
                userId, 
                creditCardNumber, 
                creditCardHolder
            ) VALUES (
                ?,
                ?,
                ?
            );", array($userId, $cardNumber, $nameOnCard));

            $orderId = $con->lastInsertId();
            foreach ($bookIds as $bookId) {
                self::query($con, "
                    INSERT INTO orderedbooks (
                        orderId, bookId
                    ) VALUES (
                        ?, ?
                    );", array($orderId, $bookId));
        
             }
             $con->commit();
        
      }
      catch (Exception $e) {
          $con->rollBack();
          $orderId = null;
      }
      self::closeConnection();
      return $orderId;
    }

}