<html>
    <style>
        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.53;
            color: #697a8d;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.4375rem 1.25rem;
            font-size: 0.9375rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease-in-out;
        }
        .btn-primary {
            color: #fff;
            background-color: #696cff;
            border-color: #696cff;
            box-shadow: 0 0.125rem 0.25rem 0 rgb(105 108 255 / 40%);
        }
    </style>
    <body>
        <h2>New Indent Approval Request</h2>
        <p>Indent({{ $indent_code }}) raised by {{ $user }} Click on the below button to view indent details.</p>
        <a class="btn btn-primary" href="{{ route('indent.show', $indent_id) }}" style="display: inline-block;
        border: 1px solid transparent;
        padding: 0.4375rem 1.25rem;
        font-size: 0.9375rem;
        border-radius: 0.375rem;
        color: #fff;
        background-color: #696cff;
        border-color: #696cff;
        text-decoration: none;">Click here</a>
    </body>
</html>
