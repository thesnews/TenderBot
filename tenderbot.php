<?php
/**
 * Tenderbot
 *
 * Dirt simple webhook to create a new TODO from a new Tender discussion. See
 * README.md for install and usage.
 *
 * @author: Mike Joseph <mike@statenews.com>
 * @copyright: 2012 State News, Inc.
 * @license: MIT
 * @link: http://statenews.com
 */

$config = array(
    'account'   => 'ACCOUNTID',
    'user'      => 'USERNAME',
    'password'  => 'PASSWORD',
    'project'   => 'PROJECTID',
    'todolist'  => 'TODOLISTID',
);

$handle = fopen('php://input','r');
$data = '';

while( ($line = fgets($handle)) ) {
    $data .= $line;
}

if( !$data ) {
    exit(1);
}

$data = json_decode($data);

$request_email = $data->author_email;
$request_user = $data->author_name;
$request_content = $data->body;
$request_url = $data->discussion->html_href;
$request_title = $data->discussion->title;

$bc_url = sprintf(
    'https://basecamp.com/%s/api/v1/projects/%s/todolists/%s/todos.json',
    $config['account'],
    $config['project'],
    $config['todolist']
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $bc_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_USERAGENT, sprintf('TenderBot (%s)', $config['user']));
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, sprintf(
    "%s:%s", $config['user'], $config['password']
));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    'content' => 'TENDER: '.$request_title
)));

$return = json_decode(curl_exec($ch));
curl_close($ch);

$todo_id = $return->id;

$bc_url = sprintf(
    'https://basecamp.com/%s/api/v1/projects/%s/todos/%s/comments.json',
    $config['account'],
    $config['project'],
    $todo_id
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $bc_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_USERAGENT, sprintf('TenderBot (%s)', $config['user']));
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, sprintf(
    "%s:%s", $config['user'], $config['password']
));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    'content' => implode("\n\n", array(
        sprintf("Requester: %s (%s)", $request_user, $request_email),
        sprintf("Discussion: %s", $request_url),
        sprintf("Request:\n%s", $request_content)
    ))
)));

curl_exec($ch);
curl_close($ch);