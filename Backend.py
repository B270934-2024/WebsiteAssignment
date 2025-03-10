#!/bin/python3
import time
import sys,os,re,matplotlib,numpy,requests
import subprocess as sp
from Bio import SeqIO
fastlist = []
s=[]
#sp.call("sh -c '$(curl -fsSL https://ftp.ncbi.nlm.nih.gov/entrez/entrezdirect/install-edirect.sh)'")
with open("pepresults.txt","w") as pepres:
    pepres.write("ID\tMolecularWeight\tResidueCount\tResidueWeight\tIsoelectricPoint\tExtinctionReduced\tExtinctionBridges\tReducedMgMl\tBridgeMgMl\tProbability(+/-)\n")
with open("results.fasta","w") as fasta:
    fasta.write("")
with open("resultsprosite.tsv","w") as proresults:
    proresults.write("SeqName\tStart\tEnd\tScore\tStrahd\tMotif\n")
with open("alignment.fasta","w") as align:
    align.write("")

#PROSITE_URL = "https://prosite.expasy.org/cgi-bin/prosite/PSScan.cgi"
species=input("What organism group??\n")
protein=input("What protein or protein family?\n")
fastlist = []
search="esearch -db protein -query \"\'"+species+"\'*[Organism] AND \'"+protein+"\'*[Protein]\"| efetch -format fasta >> results.fasta"

try:
    sp.call(search,shell=True)
except:
    print("An Error Occured.")   
    species=input("What organism group??\n")
    protein=input("What protein or protein family?\n")
    search="esearch -db protein -query \"\'"+species+"\'*[Organism] AND \'"+protein+"\'*[Protein]\"| efetch -format fasta >> results.fasta"


count=0
with open("results.fasta","r") as fasta:
    for line in fasta:
        if re.findall(".*>.*",line):
            line=line.upper().rstrip("\n")
            count +=1
            i=str(re.findall(">\w*",line))
            fastlist.append(i[3:-2])
if count==0:
    print("Error: 0 Proteins found. Please try again.")
    exit()
else:
    print(str(count)+" proteins found.")
    with open("results.tsv","w") as tabdata:
        tabdata.write("")
    search="esearch -db gene -query \"\'"+species+"\'[Organism] AND \'"+protein+"\*'[Protein]\"| efetch -format tabular >> results.tsv"
    
    sp.call(search,shell=True)   
    sp.call("plotcon -sequences results.fasta -winsize 10 -graph png",shell=True)
    sp.call("pepstats results.fasta -outfile pepstats.txt",shell=True)
    with open("pepstats.txt","r") as pep:
        with open("pepresults.txt","a") as pepres:
            seqid=molweight=resi=resweight=charge=ipoint=reduced=bridge=reducedex=bridgeex=expression_prob=None
            for lines in pep:
                lines=lines.strip()     
                if "PEPSTATS of " in lines:
                    if all(v is not None for v in (seqid, molweight, resi, resweight, charge, ipoint, reduced, bridge,reducedex,bridgeex, expression_prob)):
                        #print(f"{seqid}\t{molweight}\t{resi}\t{resweight}\t{charge}\t{ipoint}\t{reduced}\t{bridge}\t{reducedex}\t{bridgeex}\t{expression_prob}\n")
                        pepres.write(f"{seqid}\t{molweight}\t{resi}\t{resweight}\t{charge}\t{ipoint}\t{reduced}\t{bridge}\t{reducedex}\t{bridgeex}\t{expression_prob}\n")
                    seqid=molweight=resi=resweight=charge=ipoint=reduced=bridge=reducedex=bridgeex=expression_prob=None
                    seqid=re.search(r"PEPSTATS of ([A-Za-z]+.+[^\s]) from",lines).group(1)
                elif re.search(r"Molecular weight\s+=\s+", lines):
                    molweight=re.search(r"Molecular weight\s+=\s+([\d\.\-]+)",lines).group(1) 
                    resi=re.search(r"Residues = ([\d]+)",lines).group(1) 
                elif re.search(r"Charge\s+=\s+", lines):
                    resweight=re.search(r"Average Residue Weight\s+=\s+([\d\.\-]+)",lines).group(1) 
                    charge=re.search(r"Charge\s+=\s+([\d\.\-]+)",lines).group(1)
                elif re.search(r"Isoelectric Point\s+=\s+", lines):
                    ipoint=re.search(r"([\d\.]+)",lines).group(1)
                elif "A280 Molar" in lines:
                    reduced = re.search(r"(\d+)\s+\(reduced\)", lines).group(1)
                    bridge = re.search(r"(\d+)\s+\(cystine",lines).group(1)  
                elif "A280 Extinction" in lines:
                    reducedex = re.search(r"(\d+)\s+\(reduced\)", lines).group(1) 
                    bridgeex = re.search(r"(\d+)\s+\(cystine",lines).group(1)
                elif "Improbability" in lines:
                    expression_prob ="-"+ re.search(r"([\d\.]+)$", lines).group(1) 
                elif "Probability" in lines:
                    expression_prob=re.search(r"([\d\.]+)$",lines).group(1)
            if all(v is not None for v in (seqid, molweight, resi, resweight, charge, ipoint, reduced, bridge,reducedex,bridgeex, expression_prob)):
                #print(f"{seqid}\t{molweight}\t{resi}\t{resweight}\t{charge}\t{ipoint}\t{reduced}\t{bridge}\t{reducedex}\t{bridgeex}\t{expression_prob}\n")
                pepres.write(f"{seqid}\t{molweight}\t{resi}\t{resweight}\t{charge}\t{ipoint}\t{reduced}\t{bridge}\t{reducedex}\t{bridgeex}\t{expression_prob}\n")

    with open("results.fasta","r") as fasta:
        for record in SeqIO.parse(fasta,"fasta"):
            #Read sequences from FASTA file
            s.append(record)
            with open("tmp.fasta","w") as tmp:
                SeqIO.write(record,tmp,"fasta")
            sp.call(f"patmatmotifs -sequence tmp.fasta -outfile \"{record.id}.tsv\" -noprune -auto -rformat excel",shell=True)
            if os.path.exists(f"{record.id}.tsv"):
                with open(f"{record.id}.tsv","r") as add:
                    motifres=add.readlines()[1:]
                with open("resultsprosite.tsv","a") as result:
                    result.writelines(motifres)        
                os.remove(f"{record.id}.tsv")

         #print("done")
        #with open("resultsprosite.tsv","a") as proresults:
        #    for seqnum,record in enumerate(s):
        #        print(f"processing {record.id}")
        #        response = requests.post(PROSITE_URL, data={"seq":str(record.seq),"output": "json"})
        #        time.sleep(1)
        #        if response.status_code!=200:
        #            print(f"Error: PROSITE request failed (Status {response.status_code})")
        #            print("Response content:", response.text)  # Debug: Show error message
        #        results=response.json()
        #        matches=results.get("matchset",[])
        #        if matches:
        #            ascdesclist=[]
         #           ascasclist=[match.get("signature_ac","Unknown") for match in matches]
         #           ascasc="; ".join(ascasclist)
         #           for ac in ascasclist:
         #               response=requests.get(f"https://prosite.expasy.org/{ac}.txt")
         #               if response.status_code == 200:
         #                   for line in response.text.split("\n"):
         #                       if line.startswith("DE   "):  # 'DE' line contains the description
         #                         ascdesclist.append(line.replace("DE   ", "").strip())
         #           ascdesc="; ".join(ascdesclist)
         #       else:
         #           ascasc="None"
         #           ascdesc="None"
                #proresults.write(f"{record.id}\t{ascasc}\t{ascdesc}\n")
        #for seqnum,record in enumerate(s):
            #sp.call(f"patmatmotifs -sformat raw -sprotein Y -sequence {record.seq} -outfile {record.id} -full -rformat excel",shell=True)
            #print(f"done {seqnum}")
    if count <=100:
        sp.call("clustalo -i results.fasta -o alignment.fasta",shell=True)
    else:
        sp.call("mafft --auto results.fasta > alignment.fasta",shell=True)
exit()

