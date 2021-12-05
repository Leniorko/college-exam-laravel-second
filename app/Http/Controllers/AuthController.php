<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function handleRegister(Request $request)
    {

        if ($request->isMethod("post")) {

            $messages = [
                "fullname.required" => "Вы забыли ввести своё имя.",
                "fullname.regex" => "Имя должно быть в формате Имя Фамилия Отчество",
                "login.required" => "Вы забыли ввести логин",
                "login.unique" => "Пользователь с таким логином уже зарегестрирован в системе",
                "password.required" => "Вы не ввели пароль",
                "password_repeat.required" => "Вы не повторили пароль",
                "password_repeat.same" => "Пароли не совпадают",
                "data_proceed_agreement" => "Пожалуйста, дайте своё согласие на обработку данных"
            ];

            $data = $request->validate([
                "fullname" => "required|regex:/[а-яА-Я]+ [а-яА-Я]+ [а-яА-Я]+/i",
                "login" => "required|unique:users",
                "email" => "required|unique:users|email",
                "data_proceed_agreement" => "required",
                "password" => "required",
                "password_repeat" => "required|same:password"
            ], $messages);

            $new_user = new User();
            $new_user->fullname = $data["fullname"];
            $new_user->login = $data["login"];
            $new_user->email = $data["email"];
            $new_user->password = Hash::make($data["password"]);
            $new_user->data_proceed_agreement = $data["data_proceed_agreement"];

            $new_user->save();
            $new_user->refresh();

            if (Auth::attempt($new_user)) {
                $request->session()->regenerate();
                return redirect("list_tickets");
            } else {
                redirect("register")->withErrors("login_error", "Произошла ошибка после регестрации. Попробуйте войти вручную.");
            }
        }

        return view("pages.auth.register");
    }

    public function handleLogin(Request $request)
    {
        if ($request->isMethod("post")) {
            $data = $request->validate([
                "login" => "required",
                "password" => "required"
            ]);

            if (Auth::attempt($data)) {
                $request->session()->regenerate();
                return redirect("list_tickets");
            } else {
                return redirect("login")->withErrors("login_error", "Возможно вы ввели неправильные данные");
            }
        }

        return view("pages.auth.register");
    }

    public function exit(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('home');
    }
}
