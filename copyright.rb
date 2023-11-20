#!/usr/bin/env ruby

COPYRIGHT = <<EOM

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
EOM

def evaluateFile(filePath, startTag, endTag)
    fullCopyright = "#{startTag}\n#{COPYRIGHT}\n#{endTag}"

    if copyrightTagExists(startTag, endTag, filePath)
        if copyrightNeedsUpdate(
            startTag,
            endTag,
            filePath
        )
            puts "Updating copyright in #{filePath}...".brown
            replace(filePath, startTag, endTag, "\n#{COPYRIGHT}\n")
        else
            puts "No changes needed in #{filePath}, skipping...".green
        end
    else
        puts "Copyright not found in #{filePath}, adding copyright...".brown
        insert(filePath, fullCopyright)
    end
end

def copyrightTagExists(startTag, endTag, filePath)
    !File.read(filePath).scan(/(?<=#{Regexp.escape(startTag)})([\S\s]*?)(?=#{Regexp.escape(endTag)})/m).empty?
end

def copyrightNeedsUpdate(startTag, endTag, filePath)
    File.read(filePath).scan(/(?<=#{Regexp.escape(startTag)})(\n#{Regexp.escape(COPYRIGHT)}\n)(?=#{Regexp.escape(endTag)})/m).empty?
end

def insert(filePath, tag)
    # If is a php file, insert after the opening php tag
    if !(filePath =~ /\.blade\.php\z/) && File.extname(filePath) == '.php'
        content = File.read(filePath)
        File.write(filePath, content.gsub(/<\?php/, "<?php\n\n#{tag}\n"))
    else
        File.write(filePath, "#{tag}\n\n#{File.read(filePath)}")
    end
end

def replace(filePath, startTag, endTag, replacement)
    content = File.read(filePath)
    File.write(filePath, content.gsub(/(?<=#{Regexp.escape(startTag)})[\S\s]*?(?=#{Regexp.escape(endTag)})/m, replacement))
end

def blade(filePath)
    startTag = "{{--\n<COPYRIGHT>"
    endTag = "</COPYRIGHT>\n--}}"

    evaluateFile(filePath, startTag, endTag)
end

def php(filePath)
    startTag = "/*\n<COPYRIGHT>"
    endTag = "</COPYRIGHT>\n*/"

    evaluateFile(filePath, startTag, endTag)
end

def js(filePath)
    startTag = "/*\n<COPYRIGHT>"
    endTag = "</COPYRIGHT>\n*/"

    evaluateFile(filePath, startTag, endTag)
end

def css(filePath)
    startTag = "/*\n<COPYRIGHT>"
    endTag = "</COPYRIGHT>\n*/"

    evaluateFile(filePath, startTag, endTag)
end

def vue(filePath)
    startTag = "<!--\n<COPYRIGHT>"
    endTag = "</COPYRIGHT>\n-->"

    evaluateFile(filePath, startTag, endTag)
end

def handle(filePath)
  if filePath =~ /\.blade\.php\z/
    blade(filePath)
  else
    case File.extname(filePath)
        when '.php'
          php(filePath)
        when '.js'
          js(filePath)
        when '.css'
          css(filePath)
        when '.vue'
          vue(filePath)
        else
            puts "Unknown file type: #{filePath}, skipping..."
    end
  end
end

class String
def black;          "\e[30m#{self}\e[0m" end
def red;            "\e[31m#{self}\e[0m" end
def green;          "\e[32m#{self}\e[0m" end
def brown;          "\e[33m#{self}\e[0m" end
def blue;           "\e[34m#{self}\e[0m" end
def magenta;        "\e[35m#{self}\e[0m" end
def cyan;           "\e[36m#{self}\e[0m" end
def gray;           "\e[37m#{self}\e[0m" end

def bg_black;       "\e[40m#{self}\e[0m" end
def bg_red;         "\e[41m#{self}\e[0m" end
def bg_green;       "\e[42m#{self}\e[0m" end
def bg_brown;       "\e[43m#{self}\e[0m" end
def bg_blue;        "\e[44m#{self}\e[0m" end
def bg_magenta;     "\e[45m#{self}\e[0m" end
def bg_cyan;        "\e[46m#{self}\e[0m" end
def bg_gray;        "\e[47m#{self}\e[0m" end

def bold;           "\e[1m#{self}\e[22m" end
def italic;         "\e[3m#{self}\e[23m" end
def underline;      "\e[4m#{self}\e[24m" end
def blink;          "\e[5m#{self}\e[25m" end
def reverse_color;  "\e[7m#{self}\e[27m" end
end

# [
#     'app-modules/assistant/src/Filament/Pages/PersonalAssistant.php',
#     'app-modules/assistant/resources/views/filament/pages/personal-assistant.blade.php',
#     'widgets/form/src/App.vue',
#     'widgets/form/src/widget.js',
#     'widgets/form/src/widget.css',
# ].each do |file|
#     handle(file)
# end

system('git', 'add', '.')

`git ls-files`.split.each do |file|
  handle(file.chomp)
end