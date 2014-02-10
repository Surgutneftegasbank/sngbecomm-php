# generate html from markdown
echo "Clean folder"
rm *.html
rm *.aux
rm *.log
rm *.out
rm *.pdf

echo "Generating new documentation"
pandoc CHANGELOG.md -s -o changelog.html
pandoc TODO.md -s -o todo.html
pandoc ./sampleshop/CHANGELOG.md -s -o ./sampleshop/changelog.html
pandoc ./sampleshop/README.md -s -o ./sampleshop/readme.html
pandoc ./sampleshop/TODO.md -s -o ./sampleshop/todo.html

#iconv ecommerce_checklist.html -f utf-8 -t windows-1251 > ecm.html

#wkhtmltopdf faq.html faq.pdf
#wkhtmltopdf checklist.html checklist.pdf
#wkhtmltopdf clients.html clients.pdf

echo "Success! New documentation generated."
