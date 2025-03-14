#!/bin/python3
import time
import sys,os,re,numpy,requests
import subprocess as sp
from Bio import SeqIO
from Bio import Entrez
s=[]
user_id = sys.argv[1] if len(sys.argv) > 1 else "unknown"
species= sys.argv[2] if len(sys.argv) > 1 else "aves"
protein = sys.argv[3] if len(sys.argv) > 1 else "glucose-6-phosphatase"
#sp.call("sh -c '$(curl -fsSL https://ftp.ncbi.nlm.nih.gov/entrez/entrezdirect/install-edirect.sh)'",shell=True)
#sp.call("export PATH=${HOME}/edirect:${PATH}",shell=True)
#sp.call("sh -c \"curl -fsSL https://ftp.ncbi.nlm.nih.gov/entrez/entrezdirect/install-edirect.sh\"")
protein_ids=[]
with open(f"{species}_{protein}_{user_id}pepresults.txt","w") as pepres:
    pepres.write("ID\tMolecularWeight\tResidueCount\tResidueWeight\tCharge\tIsoelectricPoint\tExtinctionReduced\tExtinctionBridges\tReducedMgMl\tBridgeMgMl\tProbability_pos_neg\n")
with open(f"{species}_{protein}_{user_id}results.fasta","w") as fasta:
    fasta.write("")
with open(f"{species}_{protein}_{user_id}resultsprosite.tsv","w") as proresults:
    proresults.write("SeqName\tStart\tEnd\tScore\tStrand\tMotif\n")
with open(f"{user_id}alignment.fasta","w") as align:
    align.write("")
#PROSITE_URL = "https://prosite.expasy.org/cgi-bin/prosite/PSScan.cgi"
#species=input("What organism group??\n")
#protein=input("What protein or protein family?\n")
#search=f"esearch -db protein -query \"\'{species}\'*[Organism] AND \'{protein}\'*[Protein]\"| efetch -format fasta >> {user_id}results.fasta"
Entrez.email="s2761220@ed.ac.uk"
search_handle = Entrez.esearch(db="protein", term=f"{species}[Organism] AND {protein}[Protein]", retmax=10)
search_results = Entrez.read(search_handle)
search_handle.close()
protein_ids = search_results['IdList']
if protein_ids:
    # Fetch the sequences for the found protein IDs
    fetch_handle = Entrez.efetch(db="protein", id=protein_ids, rettype="fasta", retmode="text")
    fasta_data = fetch_handle.read()
    fetch_handle.close()
    file_name = f"{species}_{protein}_{user_id}results.fasta"
    with open(file_name, "w") as fasta_file:
        fasta_file.write(fasta_data)
#result = sp.run(search, shell=True, check=True, stdout=sp.PIPE, stderr=sp.PIPE)
#if result.returncode == 0:
#    print("ok")
    # Handle successful command
   # with open(f"{user_id}results.fasta", "w") as fasta_file:
   #print(result.stdout.decode('utf-8'))
#else:
#    print(f"An error occurred: {result.stderr.decode('utf-8')}")
#    exit()
#try:
    #sp.call(search,shell=True)
#    print("worked")
    #with open(f"{user_id}results.fasta", "w") as fasta_file:
        #fasta_file.write(result.stdout.decode('utf-8'))
#except sp.CalledProcessError as e:
 #   print("An Error Occured.")
  #  print(e.stderr.decode('utf-8'))
    #species=input("What organism group??\n")
    #protein=input("What protein or protein family?\n")
    #search="esearch -db protein -query \"\'"+species+"\'*[Organism] AND \'"+protein+"\'*[Protein]\"| efetch -format fasta >> results.fasta"

#Unsure why its not finding any results???
#count=0
#with open(f"{user_id}results.fasta","r") as fasta:
#    for line in fasta:
#        if re.findall(".*>.*",line):
#            line=line.upper().rstrip("\n")
#            count +=1
#            i=str(re.findall(">\w*",line))
#            fastlist.append(i[3:-2])
#if count==0:
#    print(search)
#    print("Error: 0 Proteins found. Please try again.")
#    exit()
    print(str(len(protein_ids))+" proteins found.")
    
    #with open(f"{user_id}results.tsv","w") as tabdata:
    # tabdata.write("")
    #search=f"esearch -db gene -query \"\'{species}\'[Organism] AND \'{protein}\*\'[Protein]\"| efetch -format tabular >> {user_id}results.tsv"
    #sp.call(search,shell=True)   
    fetch_handle = Entrez.efetch(db="protein", id=protein_ids, rettype="text", retmode="text")
    tsv_data = fetch_handle.read()
    fetch_handle.close()
    file_name = f"{species}_{protein}_{user_id}results.tsv"
    with open(file_name, "w") as tsv_file:
        tsv_file.write(tsv_data)
    
    sp.call(f"plotcon -sequences {species}_{protein}_{user_id}results.fasta -winsize 10 -graph png",shell=True)
    sp.call(f"cp plotcon.1.png {species}_{protein}_{user_id}.plotcon.png",shell=True)
    sp.call(f"pepstats {user_id}results.fasta -outfile {species}_{protein}_{user_id}pepstats.txt",shell=True)
    if len(protein_ids) < 20:
        sp.call(f"prettyplot {user_id}results.fasta -graph pdf",shell=True)
        sp.call(f"mv prettyplot.pdf {species}_{protein}_{user_id}prettyplot.pdf",shell=True)
    with open(f"{species}_{protein}_{user_id}pepstats.txt","r") as pep:
        with open(f"{species}_{protein}_{user_id}pepresults.txt","a") as pepres:
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

    with open(f"{species}_{protein}_{user_id}results.fasta","r") as fasta:
        for record in SeqIO.parse(fasta,"fasta"):
            #Read sequences from FASTA file
            s.append(record)
            with open(f"{user_id}tmp.fasta","w") as tmp:
                SeqIO.write(record,tmp,"fasta")
            sp.call(f"patmatmotifs -sequence {user_id}tmp.fasta -outfile \"{user_id}{record.id}.tsv\" -noprune -auto -rformat excel",shell=True)
            if os.path.exists(f"{user_id}{record.id}.tsv"):
                with open(f"{user_id}{record.id}.tsv","r") as add:
                    motifres=add.readlines()[1:]
                with open(f"{species}_{protein}_{user_id}resultsprosite.tsv","a") as result:
                    result.writelines(motifres)        
                os.remove(f"{user_id}{record.id}.tsv")
    os.remove(f"{user_id}tmp.fasta")
        ##check this file, turn into tsv, count # of motifs. 

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
    if len(protein_ids)<=100:
        sp.call(f"clustalo -i {species}_{protein}_{user_id}results.fasta -o {species}_{protein}_{user_id}alignment.fasta --force",shell=True)
    else:
        sp.call(f"mafft --quiet --auto {species}_{protein}_{user_id}results.fasta > {species}_{protein}_{user_id}alignment.fasta -v",shell=True)
    #print(f"{user_id}")
    #call=f"zip {user_id}results.zip ${user_id}*"
    if os.path.exists(f"{user_id}results.zip"):
        sp.call(f"unzip -o {user_id}results.zip -d {user_id}resultstmp",shell=True)
        sp.call(f"cd {user_id}_temp && zip -r ../{user_id}results *{user_id}*", shell=True)
        sp.call(f"rm -rf {user_id}_temp",shell=True)
    else:
        sp.call(f"zip {user_id}results.zip *{user_id}*",shell=True)
    #sp.call(call,shell=True)
    exit()
else:
    print("No proteins found.")
    exit()
