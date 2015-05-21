# vcf4IonTorrent
A script to modify existing Vcf files adding fields required by IonTorrent Suite. 
Description : Open a vcf file and add a AD fields, taking values of SAF SAR SRF SRR to obtain 
AD = X , Y 
where 
X = SRF+SRR 
Y = SAF+SAR  

the output is a file with the same name of original file but with the prefix "modified_"

v0.1
Mauro Lobosky Donadello lobosky@gmail.com


usage : start.sh 

The script will parse the VCF folder and the output will be written inside the modified_VFC folder