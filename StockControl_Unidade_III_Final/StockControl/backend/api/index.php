<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/../config/db.php';
function out(array $data, int $status=200): never { http_response_code($status); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
function payload(): array { $a=json_decode(file_get_contents('php://input'),true); if (!is_array($a)) out(['erro'=>'JSON inválido'],400); return $a; }
function requiredText(array $a,string $key,int $max=150): string { $v=trim((string)($a[$key]??'')); if ($v==='' || mb_strlen($v)>$max) out(['erro'=>"Campo $key obrigatório ou muito longo"],422); return $v; }
function positiveInt(mixed $v,string $field): int { if (filter_var($v,FILTER_VALIDATE_INT)===false || (int)$v<=0) out(['erro'=>"$field deve ser inteiro positivo"],422); return (int)$v; }
function nonNegativeInt(mixed $v,string $field): int { if (filter_var($v,FILTER_VALIDATE_INT)===false || (int)$v<0) out(['erro'=>"$field deve ser inteiro não negativo"],422); return (int)$v; }
function csrf(): void { if (!hash_equals($_SESSION['csrf']??'',(string)($_SERVER['HTTP_X_CSRF_TOKEN']??''))) out(['erro'=>'Token de segurança inválido'],403); }
function auth(): int { if (empty($_SESSION['user_id'])) out(['erro'=>'Faça login para continuar'],401); return (int)$_SESSION['user_id']; }
$method=$_SERVER['REQUEST_METHOD']; $action=$_GET['action']??'';
try {
    if ($action==='setup-status' && $method==='GET') { $n=(int)db()->query('SELECT COUNT(*) FROM usuarios')->fetchColumn(); out(['precisa_configurar'=>$n===0]); }
    if ($action==='setup' && $method==='POST') {
        $a=payload(); $pdo=db(); $pdo->beginTransaction();
        $count=(int)$pdo->query('SELECT COUNT(*) FROM usuarios FOR UPDATE')->fetchColumn();
        if ($count!==0) { $pdo->rollBack(); out(['erro'=>'Configuração inicial já realizada'],403); }
        $name=requiredText($a,'nome'); $email=requiredText($a,'email'); $password=(string)($a['senha']??'');
        if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<10) { $pdo->rollBack(); out(['erro'=>'E-mail inválido ou senha com menos de 10 caracteres'],422); }
        $s=$pdo->prepare('INSERT INTO usuarios(nome,email,senha) VALUES(?,?,?)'); $s->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
        $pdo->commit(); out(['mensagem'=>'Usuário inicial criado']);
    }
    if ($action==='login' && $method==='POST') {
        $a=payload(); $s=db()->prepare('SELECT id,nome,senha FROM usuarios WHERE email=? AND ativo=1'); $s->execute([trim((string)($a['email']??''))]); $u=$s->fetch();
        if (!$u || !password_verify((string)($a['senha']??''),$u['senha'])) out(['erro'=>'Credenciais inválidas'],401);
        session_regenerate_id(true); $_SESSION['user_id']=$u['id']; $_SESSION['nome']=$u['nome']; $_SESSION['csrf']=bin2hex(random_bytes(24));
        out(['nome'=>$u['nome'],'csrf'=>$_SESSION['csrf']]);
    }
    if ($action==='me' && $method==='GET') { auth(); out(['nome'=>$_SESSION['nome'],'csrf'=>$_SESSION['csrf']]); }
    if ($action==='logout' && $method==='POST') { auth(); csrf(); $_SESSION=[]; session_destroy(); out(['mensagem'=>'Sessão encerrada']); }
    $uid=auth();
    if ($action==='catalogos' && $method==='GET') {
        out(['categorias'=>db()->query('SELECT id,nome FROM categorias ORDER BY nome')->fetchAll(), 'fornecedores'=>db()->query('SELECT id,nome FROM fornecedores WHERE ativo=1 ORDER BY nome')->fetchAll()]);
    }
    if ($action==='produtos' && $method==='GET') {
        $sql='SELECT p.id,p.nome,p.sku,p.descricao,p.quantidade,p.estoque_minimo,p.preco_custo,p.ativo,p.categoria_id,p.fornecedor_id,c.nome categoria,f.nome fornecedor FROM produtos p JOIN categorias c ON c.id=p.categoria_id JOIN fornecedores f ON f.id=p.fornecedor_id ORDER BY p.nome';
        out(['produtos'=>db()->query($sql)->fetchAll()]);
    }
    if ($action==='produtos' && $method==='POST') {
        csrf(); $a=payload(); $nome=requiredText($a,'nome'); $sku=requiredText($a,'sku',50);
        $cat=positiveInt($a['categoria_id']??null,'categoria_id'); $for=positiveInt($a['fornecedor_id']??null,'fornecedor_id');
        $min=nonNegativeInt($a['estoque_minimo']??5,'estoque_minimo'); $price=$a['preco_custo']??0;
        if (!is_numeric($price) || (float)$price<0 || (float)$price>99999999.99) out(['erro'=>'Preço inválido'],422);
        $s=db()->prepare('INSERT INTO produtos(nome,descricao,sku,categoria_id,fornecedor_id,quantidade,estoque_minimo,preco_custo) VALUES(?,?,?,?,?,0,?,?)');
        $s->execute([$nome,trim((string)($a['descricao']??'')),$sku,$cat,$for,$min,$price]); out(['mensagem'=>'Produto cadastrado','id'=>(int)db()->lastInsertId()],201);
    }
    if ($action==='produto-atualizar' && $method==='POST') {
        csrf(); $a=payload(); $id=positiveInt($a['id']??null,'id');
        $nome=requiredText($a,'nome'); $sku=requiredText($a,'sku',50);
        $cat=positiveInt($a['categoria_id']??null,'categoria_id');
        $for=positiveInt($a['fornecedor_id']??null,'fornecedor_id');
        $min=nonNegativeInt($a['estoque_minimo']??null,'estoque_minimo');
        $price=$a['preco_custo']??null;
        if (!is_numeric($price) || (float)$price<0 || (float)$price>99999999.99) out(['erro'=>'Preço inválido'],422);
        $descricao=mb_substr(trim((string)($a['descricao']??'')),0,255);
        $stmt=db()->prepare('UPDATE produtos SET nome=?,sku=?,categoria_id=?,fornecedor_id=?,estoque_minimo=?,preco_custo=?,descricao=? WHERE id=? AND ativo=1');
        $stmt->execute([$nome,$sku,$cat,$for,$min,$price,$descricao,$id]);
        if (!$stmt->rowCount()) {
            $check=db()->prepare('SELECT id FROM produtos WHERE id=? AND ativo=1'); $check->execute([$id]);
            if (!$check->fetch()) out(['erro'=>'Produto inexistente ou inativo'],404);
        }
        out(['mensagem'=>'Produto atualizado']);
    }
    if ($action==='produto-inativar' && $method==='POST') {
        csrf(); $a=payload(); $id=positiveInt($a['id']??null,'id');
        $stmt=db()->prepare('UPDATE produtos SET ativo=0 WHERE id=? AND ativo=1'); $stmt->execute([$id]);
        if (!$stmt->rowCount()) out(['erro'=>'Produto inexistente ou já inativo'],404);
        out(['mensagem'=>'Produto inativado']);
    }
    if ($action==='produto-reativar' && $method==='POST') {
        csrf(); $a=payload(); $id=positiveInt($a['id']??null,'id');
        $s=db()->prepare('UPDATE produtos SET ativo=1 WHERE id=? AND ativo=0'); $s->execute([$id]);
        if (!$s->rowCount()) out(['erro'=>'Produto inexistente ou já ativo'],404);
        out(['mensagem'=>'Produto reativado']);
    }
    if ($action==='fornecedores' && $method==='GET') {
        out(['fornecedores'=>db()->query('SELECT id,nome,cnpj,telefone,email,endereco,ativo FROM fornecedores ORDER BY nome')->fetchAll()]);
    }
    if ($action==='fornecedores' && $method==='POST') {
        csrf(); $a=payload(); $nome=requiredText($a,'nome');
        $cnpj=preg_replace('/\D/','',(string)($a['cnpj']??''));
        if ($cnpj!=='' && strlen($cnpj)!==14) out(['erro'=>'CNPJ deve ter 14 dígitos'],422);
        $email=trim((string)($a['email']??''));
        if (strlen($email)>150 || ($email!=='' && !filter_var($email,FILTER_VALIDATE_EMAIL))) out(['erro'=>'E-mail inválido'],422);
        $telefone=trim((string)($a['telefone']??'')); $endereco=trim((string)($a['endereco']??''));
        if (mb_strlen($telefone)>20 || mb_strlen($endereco)>255) out(['erro'=>'Telefone ou endereço muito longo'],422);
        $s=db()->prepare('INSERT INTO fornecedores(nome,cnpj,telefone,email,endereco) VALUES(?,?,?,?,?)');
        $s->execute([$nome,$cnpj?:null,$telefone,$email,$endereco]);
        out(['mensagem'=>'Fornecedor cadastrado','id'=>(int)db()->lastInsertId()],201);
    }
    if ($action==='fornecedor-atualizar' && $method==='POST') {
        csrf(); $a=payload(); $id=positiveInt($a['id']??null,'id'); $nome=requiredText($a,'nome');
        $cnpj=preg_replace('/\D/','',(string)($a['cnpj']??''));
        if ($cnpj!=='' && strlen($cnpj)!==14) out(['erro'=>'CNPJ deve ter 14 dígitos'],422);
        $email=trim((string)($a['email']??''));
        if (strlen($email)>150 || ($email!=='' && !filter_var($email,FILTER_VALIDATE_EMAIL))) out(['erro'=>'E-mail inválido'],422);
        $telefone=trim((string)($a['telefone']??'')); $endereco=trim((string)($a['endereco']??''));
        if (mb_strlen($telefone)>20 || mb_strlen($endereco)>255) out(['erro'=>'Telefone ou endereço muito longo'],422);
        $s=db()->prepare('UPDATE fornecedores SET nome=?,cnpj=?,telefone=?,email=?,endereco=? WHERE id=? AND ativo=1');
        $s->execute([$nome,$cnpj?:null,$telefone,$email,$endereco,$id]);
        $check=db()->prepare('SELECT id FROM fornecedores WHERE id=? AND ativo=1'); $check->execute([$id]);
        if (!$check->fetch()) out(['erro'=>'Fornecedor inexistente ou inativo'],404);
        out(['mensagem'=>'Fornecedor atualizado']);
    }
    if (in_array($action,['fornecedor-inativar','fornecedor-reativar'],true) && $method==='POST') {
        csrf(); $a=payload(); $id=positiveInt($a['id']??null,'id');
        $to=$action==='fornecedor-reativar'?1:0;
        $s=db()->prepare('UPDATE fornecedores SET ativo=? WHERE id=? AND ativo<>?'); $s->execute([$to,$id,$to]);
        if (!$s->rowCount()) out(['erro'=>'Fornecedor inexistente ou já está nesse estado'],404);
        out(['mensagem'=>$to?'Fornecedor reativado':'Fornecedor inativado']);
    }
    if ($action==='categorias' && $method==='GET') {
        out(['categorias'=>db()->query('SELECT id,nome,descricao FROM categorias ORDER BY nome')->fetchAll()]);
    }
    if ($action==='categorias' && $method==='POST') {
        csrf(); $a=payload(); $nome=requiredText($a,'nome',100);
        $descricao=trim((string)($a['descricao']??''));
        if (mb_strlen($descricao)>255) out(['erro'=>'Descrição muito longa'],422);
        $s=db()->prepare('INSERT INTO categorias(nome,descricao) VALUES(?,?)'); $s->execute([$nome,$descricao]);
        out(['mensagem'=>'Categoria cadastrada','id'=>(int)db()->lastInsertId()],201);
    }
    if ($action==='movimentacoes' && $method==='GET') {
        $sql='SELECT m.id,m.tipo,m.quantidade,m.observacao,m.criado_em,p.nome produto,u.nome usuario FROM movimentacoes m JOIN produtos p ON p.id=m.produto_id JOIN usuarios u ON u.id=m.usuario_id ORDER BY m.id DESC LIMIT 100';
        out(['movimentacoes'=>db()->query($sql)->fetchAll()]);
    }
    if ($action==='movimentacoes' && $method==='POST') {
        csrf(); $a=payload(); $pid=positiveInt($a['produto_id']??null,'produto_id'); $q=positiveInt($a['quantidade']??null,'quantidade');
        $tipo=$a['tipo']??''; if (!in_array($tipo,['ENTRADA','SAIDA'],true)) out(['erro'=>'Tipo inválido'],422);
        $pdo=db(); $pdo->beginTransaction();
        $s=$pdo->prepare('SELECT quantidade,ativo FROM produtos WHERE id=? FOR UPDATE'); $s->execute([$pid]); $p=$s->fetch();
        if (!$p || !(int)$p['ativo']) { $pdo->rollBack(); out(['erro'=>'Produto inexistente ou inativo'],404); }
        if ($tipo==='SAIDA' && (int)$p['quantidade']<$q) { $pdo->rollBack(); out(['erro'=>'Estoque insuficiente'],422); }
        $new=(int)$p['quantidade']+($tipo==='ENTRADA'?$q:-$q);
        if ($new>2147483647) { $pdo->rollBack(); out(['erro'=>'Quantidade excede limite'],422); }
        $s=$pdo->prepare('UPDATE produtos SET quantidade=? WHERE id=?'); $s->execute([$new,$pid]);
        $s=$pdo->prepare('INSERT INTO movimentacoes(produto_id,usuario_id,tipo,quantidade,observacao) VALUES(?,?,?,?,?)');
        $s->execute([$pid,$uid,$tipo,$q,mb_substr(trim((string)($a['observacao']??'')),0,255)]);
        $pdo->commit(); out(['mensagem'=>'Movimentação registrada','saldo'=>$new],201);
    }
    out(['erro'=>'Rota não encontrada'],404);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    error_log($e->getMessage());
    if ($e->getCode()==='23000') out(['erro'=>'Registro duplicado ou relacionamento inválido'],409);
    out(['erro'=>'Erro de banco de dados. Confira as configurações e os logs do PHP.'],500);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    error_log($e->getMessage()); out(['erro'=>'Erro interno'],500);
}
