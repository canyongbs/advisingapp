#!/usr/bin/env ruby

YEAR = Time.now.year;

COPYRIGHT = <<EOM

    Copyright © 2016-#{YEAR}, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor\u2019s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
EOM

# Blade-specific copyright: 6-space continuation indent reduced to 4-space,
# and empty lines padded with 4 spaces to match prettier-plugin-blade output.
BLADE_COPYRIGHT = COPYRIGHT.gsub(/^ {6}/, "    ").gsub(/^$/, "    ").sub(/\n\z/, "\n    ")

def evaluateFile(filePath, startTag, endTag, copyrightText = COPYRIGHT)
    fullCopyright = "#{startTag}\n#{copyrightText}\n#{endTag}"

    if copyrightTagExists(startTag, endTag, filePath)
        if copyrightNeedsUpdate(
            startTag,
            endTag,
            filePath,
            copyrightText
        )
            puts "Updating copyright in #{filePath}...".brown
            replace(filePath, startTag, endTag, "\n#{copyrightText}\n")
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

def copyrightNeedsUpdate(startTag, endTag, filePath, copyrightText = COPYRIGHT)
    File.read(filePath).scan(/(?<=#{Regexp.escape(startTag)})(\n#{Regexp.escape(copyrightText)}\n)(?=#{Regexp.escape(endTag)})/m).empty?
end

def insert(filePath, tag)
    # If is a php file, insert after the opening php tag
    if !(filePath =~ /\.blade\.php\z/) && File.extname(filePath) == '.php'
        content = File.read(filePath)
        File.write(filePath, content.gsub(/<\?php/, "<?php\n\n#{tag}"))
    else
        File.write(filePath, "#{tag}\n#{File.read(filePath)}")
    end
end

def replace(filePath, startTag, endTag, replacement)
    content = File.read(filePath)
    File.write(filePath, content.gsub(/(?<=#{Regexp.escape(startTag)})[\S\s]*?(?=#{Regexp.escape(endTag)})/m, replacement))
end

def blade(filePath)
    oldStartTag = "{{--\n<COPYRIGHT>"
    oldEndTag = "</COPYRIGHT>\n--}}"
    newStartTag = "{{--\n    <COPYRIGHT>"
    newEndTag = "    </COPYRIGHT>\n--}}"

    content = File.read(filePath)

    # Migrate old tag format to new indented format (prettier-compatible)
    if content.include?(oldStartTag)
        content = content.sub(oldStartTag, newStartTag)
                         .sub(oldEndTag, newEndTag)
        File.write(filePath, content)
    end

    evaluateFile(filePath, newStartTag, newEndTag, BLADE_COPYRIGHT)
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
            puts "Unknown file type: #{filePath}, skipping...".gray
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

system('git', 'add', '.')

`git ls-files`.split.each do |file|
  handle(file.chomp)
end
