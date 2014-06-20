<?php
//============================================
// class_CSRF.php
// CSRF:�N���X�T�C�g���N�G�X�g�t�H�[�W�F��(Cross-Site Request Forgeries)
// CSR:����̃T�C�g�̐��K�̃��[�U�̌��������p���āA���K�̃��[�U���Ӑ}���Ă��Ȃ�����������������U��
//============================================

//+++++++++++++++++++++++++++++
// class_CSRF�N���X
//+++++++++++++++++++++++++++++
class class_CSRF
{
    var $ttl;
    var $name;

    function Token($name = 'tokens', $ttl = 1800)
    {
        // CSRF ���o�g�[�N���ő�L����(�b)
        // �ŏ�����͂��̒l�� 1/2 (1800 �̏ꍇ�́A900�b�Ԃ͍Œ�ێ������)
        $this->ttl = (int)$ttl;

        // �Z�b�V�����ɓo�^����g�[�N���z��̖���
        $this->name = $name;
    }

    /**
     * �g�[�N���𐶐�
     */
    function createToken()
    {
        $curr = time();
        $tokens = isset($_SESSION[$this->name]) ? $_SESSION[$this->name] : array();
        foreach ($tokens as $id => $time) {
            // �L�����؂�̏ꍇ�̓��X�g����폜
            if ($time < $curr - $this->ttl) {
                unset($tokens[$id]);
            } else {
                $uniq_id = $id;
            }
        }
        if (count($tokens) < 2) {
            if (!$tokens || ($curr - (int)($this->ttl / 2)) >= max($tokens)) {
                $uniq_id = sha1(uniqid(rand(), TRUE));
                $tokens[$uniq_id] = time();
            }
        }
        // ���X�g���Z�b�V�����ɓo�^
        $_SESSION[$this->name] = $tokens;
        return $uniq_id;
    }

    /**
     * �Z�b�V�����̃��X�g�Ƀg�[�N�������݂��A�g�[�N�����L�������̏ꍇ�� FALSE ��Ԃ�
     */
    function isCSRF($token)
    {
        $tokens = $_SESSION[$this->name];
        if (isset($tokens[$token]) && $tokens[$token] > time() - $this->ttl) {
            return FALSE;
        }
        return TRUE;
    }
}

?>