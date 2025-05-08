{{$register = Package.Raxon.Markdown:Init:register()}}
{{if(!is.empty($register))}}
{{Package.Raxon.Markdown:Import:role.system()}}
{{Package.Raxon.Markdown:Main:markdown.install(flags(), options())}}
{{/if}}