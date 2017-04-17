<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/14
 * Time: 16:13
 */

namespace sinri\enoch\core;


class LibSession implements \SessionHandlerInterface
{
    private $session_id;
    private $session_name;
    private $savePath;

    protected function __construct()
    {
        // NOTHING
    }

    public function getSessionID()
    {
        return $this->session_id;
    }

    public function setSessionID($id)
    {
        $this->session_id = $id;
    }

    public function getSessionName()
    {
        return $this->session_name;
    }

    public function setSessionName($name)
    {
        $this->session_name = $name;
    }

    public static function sessionStart($session_dir = null)
    {
        if (!empty($session_dir)) {
            session_save_path($session_dir);
        }
        //指定本类为会话处理代理
        $handler = new LibSession();
        session_set_save_handler($handler, true);
        //启动新会话或者重用现有会话
        session_start();
        //获取当前会话 ID
        $session_id = session_id();
        $handler->setSessionID($session_id);
        //读取会话名称
        $session_name = session_name();
        $handler->setSessionName($session_name);
    }

    // interface

    /**
     * Re-initialize existing session, or creates a new one.
     * Called when a session starts or when session_start() is invoked.
     */
    public function open($savePath, $sessionName)
    {
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    /**
     * Closes the current session.
     * This function is automatically executed when closing the session,
     * or explicitly via session_write_close().
     *
     */
    public function close()
    {
        return true;
    }

    /**
     * Reads the session data from the session storage, and returns the results.
     * Called right after the session starts or when session_start() is called.
     * Please note that before this method is called SessionHandlerInterface::open() is invoked.
     */
    public function read($id)
    {
        return (string)@file_get_contents("{$this->savePath}/sess_{$id}");
    }

    /**
     * Writes the session data to the session storage.
     * Called by session_write_close(), when session_register_shutdown() fails,
     * or during a normal shutdown.
     * Note: SessionHandlerInterface::close() is called immediately after this function.
     */
    public function write($id, $data)
    {
        return file_put_contents("{$this->savePath}/sess_{$id}", $data) === false ? false : true;
    }

    /**
     * Destroys a session.
     * Called by session_regenerate_id() (with $destroy = TRUE),
     * session_destroy() and when session_decode() fails.
     */
    public function destroy($id)
    {
        $file = "{$this->savePath}/sess_{$id}";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    /**
     * Cleans up expired sessions.
     * Called by session_start(), based on session.gc_divisor,
     * session.gc_probability and session.gc_maxlifetime settings.
     */
    public function gc($maxlifetime)
    {
        foreach (glob("{$this->savePath}/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}