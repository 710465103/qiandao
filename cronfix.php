<?php
// �趨����
$phpfile = ��cron.php��;// ��Ҫִ�е�Cron�ļ���
$time = 1;// ���������
// �ر��������Ȼִ��
set_time_limit(0);
ignore_user_abort(true);
// ��ȡ��¼
$A=$B=$C=$D= 0;
$F = off;
include(��cronlog.php��);
if (time() - $A < 30)
    exit;
// �ж��Ƿ��н�����ִ��
if ($F == on)
    exit();
// �ж�D�Ƿ�Ϊ��
if ($D == 0){
    $D = $C;
    $C = $B;
    $B = $A;
    $A = time();
    writelog($A,$B,$C,$D,$F);
    include($phpfile);
    exit();
}
//  ��������
$D = $C;
$C = $B;
$B = $A;
$A = time();
$E = ($A-$D)/3;
writelog($A,$B,$C,$D,$F);
// �������д���
$time *= 60;
$i=round($E/$time);
if ($i <= 0){
    include($phpfile);
    exit();
}
if ($i > 60){
    $A=$B=$C=$D= 0;
    $F = off;
    writelog($A,$B,$C,$D,$F);
    exit();
}
// ��ֹ���������
$F = on;
writelog($A,$B,$C,$D,$F);
// ѭ��
$u=1;
while($u<=$i){
    include($phpfile);
    if ($A+$E-time()<120){
        $F = off;
        writelog($A,$B,$C,$D,$F);
    }
    if ($A+$E-time()<60)
        exit();
    sleep ($time);
    $u++;
}
exit();
// �Զ��庯��
function writelog($A,$B,$C,$D,$F){
    $file = ��<?php��.PHP_EOL.��$A = ��.$A.��;��.PHP_EOL.��$B = ��.$B.��;��.PHP_EOL.��$C = ��.$C.��;��.PHP_EOL.��$D = ��.$D.��;��.PHP_EOL.��$F = ��.$F.��;��.PHP_EOL.��?>��;
    file_put_contents(��cronlog.php��,$file);
}
?>