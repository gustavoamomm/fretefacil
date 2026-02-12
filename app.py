from flask import Flask, render_template, request, redirect, url_for, session, flash
from decimal import Decimal

app = Flask(__name__)
app.secret_key = 'segredo_super_importante'
import mysql.connector

def conectar():
    return mysql.connector.connect(
        host="tini.click",
        user="fretefacil_db",
        password="ec34854404589aea43b2fa279a9d827a",
        database="fretefacil_db"
    )

@app.route('/')
def main():
    return render_template('index.html')

@app.route('/cadastro')
def cadastro():
    return render_template('cadastro.html')

@app.route('/cadastro_pcu', methods=['GET', 'POST'])
def cadastro_pcu():

    if request.method == 'GET':
        return render_template('cadastro_pcu.html')

    nome = request.form.get('nome')
    dt_nasc = request.form.get('dt_nasc')
    cpf = request.form.get('cpf')
    cnh = request.form.get('cnh')
    senha = request.form.get('senha')
    confirsenha = request.form.get('confirsenha')

    if not (nome and dt_nasc and cpf and cnh and senha and confirsenha):
        flash('Por favor, preencha todos os campos obrigatórios.', 'erro')
        return redirect(url_for('cadastro_pcu'))

    if senha != confirsenha:
        flash('As senhas digitadas não coincidem.', 'erro')
        return redirect(url_for('cadastro_pcu'))

    try:
        conexao = conectar()
        cursor = conexao.cursor()

        # 1. Inserindo usuário PCU
        sql = """
            INSERT INTO tb_usuario_pcu (nome, dt_nasc, cpf, cnh, senha)
            VALUES (%s, %s, %s, %s, %s)
        """
        valores = (nome, dt_nasc, cpf, cnh, senha)
        cursor.execute(sql, valores)

        # 2. Pegando o ID criado
        pcu_id = cursor.lastrowid

        # 3. Criando carteira do PCU automaticamente
        sql_carteira = """
            INSERT INTO tb_carteira (saldo, pcu_id)
            VALUES (0, %s)
        """
        cursor.execute(sql_carteira, (pcu_id,))

        # 4. Commit final
        conexao.commit()

        cursor.close()
        conexao.close()

        flash('PCU cadastrado com sucesso!', 'sucesso')

    except Exception as e:
        print("Erro ao inserir PCU:", e)
        flash('Erro ao cadastrar PCU.', 'erro')

    return redirect(url_for('cadastro_pcu'))



@app.route('/cadastro_emp', methods=['GET', 'POST'])
def cadastro_emp():
    if request.method == 'GET':
        return render_template('cadastro_emp.html')

    nome = request.form.get('nome')
    cnpj = request.form.get('cnpj')
    senha = request.form.get('senha')
    confirsenha = request.form.get('confirsenha')

    if not (nome and cnpj and senha and confirsenha):
        flash('Por favor, preencha todos os campos obrigatórios.', 'erro')
        return redirect(url_for('cadastro_emp'))

    if senha != confirsenha:
        flash('As senhas digitadas não coincidem.', 'erro')
        return redirect(url_for('cadastro_emp'))

    try:
        conexao = conectar()
        cursor = conexao.cursor()

        sql = """
            INSERT INTO tb_usuario_empresa (nome, cnpj, senha)
            VALUES (%s, %s, %s)
        """
        valores = (nome, cnpj, senha)

        cursor.execute(sql, valores)
        conexao.commit()

        cursor.close()
        conexao.close()

        flash('Empresa cadastrada com sucesso!', 'sucesso')

    except Exception as e:
        print("Erro ao inserir empresa:", e)
        flash('Erro ao cadastrar empresa.', 'erro')

    return redirect(url_for('cadastro_emp'))


@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST': 
        dados = request.form.get('dados')
        senha = request.form.get('senha')

        if not dados or not senha:
            flash('Informe o CPF ou CNPJ!', 'erro')
            return render_template('login.html')
        
        try:
            conexao = conectar()
            cursor = conexao.cursor(dictionary=True)

            # LOGIN DO PCU
            sql_pcu = """
                SELECT * FROM tb_usuario_pcu
                WHERE cpf = %s AND senha = %s
            """
            cursor.execute(sql_pcu, (dados, senha))
            user = cursor.fetchone()

            if user:
                session['usuario_id'] = user['pcu_id']
                session['usuario_nome'] = user['nome']
                session['tipo'] = "pcu"
                flash("Login de PCU realizado com sucesso!", "sucesso")
                return redirect(url_for('dashboard_pcu'))
            
            # LOGIN DA EMPRESA
            sql_emp = """
                SELECT * FROM tb_usuario_empresa
                WHERE cnpj = %s AND senha = %s
            """
            cursor.execute(sql_emp, (dados, senha))
            user = cursor.fetchone()

            if user:
                session['usuario_id'] = user['id_empresa']
                session['usuario_nome'] = user['nome']
                session['tipo'] = "empresa"
                flash("Login de Empresa realizado com sucesso!", "sucesso")
                return redirect(url_for('dashboard_empresa'))
            
            flash("CPF/CNPJ ou senha incorretos!", "erro")
            return render_template('login.html')
        
        except Exception as e:
            print("ERRO NO LOGIN:", e)
            flash("Erro interno ao tentar fazer login.", "erro")
            return render_template('login.html')
        
        finally:
            try:
                cursor.close()
                conexao.close()
            except:
                pass
    
    # GET → renderiza a página
    return render_template('login.html')


@app.route('/logout')
def logout():
    session.clear()

    flash("Sessão encerrada com sucesso!", "sucesso")

    return redirect(url_for('login'))


# Dashboard principal do PCU
@app.route('/pcu/dashboard')
def dashboard_pcu():

    if 'usuario_id' not in session or session.get('tipo') != 'pcu':
        flash("Erro: usuário não identificado.", "erro")
        return redirect(url_for('login'))

    nome = session.get('usuario_nome', 'Usuário')

    return render_template(
        'dashboard_base_pcu.html',
        current_tab='fretes'
    )

@app.route('/pcu/listar_fretes')
def listar_fretes():
    pcu_id = session.get('usuario_id')

    if not pcu_id:
        flash("Erro: PCU não identificado.", "erro")
        return redirect(url_for('login'))
    
    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)

        cursor.execute("""
            SELECT 
                f.id_frete,
                f.origem,
                f.destino,
                f.valor,
                f.empresa_id,
                f.carro_necessario,
                e.nome AS empresa_nome
            FROM tb_frete f
            JOIN tb_usuario_empresa e ON f.empresa_id = e.id_empresa
            WHERE f.status = 'Pendente'
            ORDER BY f.id_frete DESC
        """)

        fretes = cursor.fetchall()

        cursor.close()
        conexao.close()

        return render_template(
            'fretes.html',
            fretes=fretes,
            current_tab='fretes'
        )
    except Exception as e:
        print("ERRO AO LISTAR FRETE:", e)
        flash("Erro ao carregar fretes disponíveis.", "erro")
        return render_template('fretes.html', fretes=[], current_tab='fretes')

    

@app.route('/pcu/registrar_carro', methods=['GET', 'POST'])
def registrar_carro():
    pcu_id = session.get('usuario_id')

    if not pcu_id:
        flash("Erro: usuário não identificado.", "erro")
        return redirect(url_for('login'))
    

    if request.method == 'POST':
        placa = request.form.get('placa')
        modelo = request.form.get('modelo')
        porte = request.form.get('porte')
        carroceria = request.form.get('carroceria')

        # Validação simples
        if not (placa and modelo and porte and carroceria):
            flash("Preencha todos os campos obrigatórios.", "erro")
            return redirect(url_for('registrar_carro'))
        
        try: 
            conexao = conectar()
            cursor = conexao.cursor()

            sql = """
                INSERT INTO tb_carro
                (placa, modelo, porte, carroceria, pcu_id)
                VALUES  (%s, %s, %s, %s, %s)
            """

            valores = (placa, modelo, porte, carroceria, pcu_id)

            cursor.execute(sql, valores)
            conexao.commit()

            cursor.close()
            conexao.close()

            flash("Carro registrado com sucesso!", "sucesso")
            return redirect(url_for('meus_carros'))
        
        except Exception as e:
            print("ERRO AO REGISTRAR CARRO:", e)
            flash("Erro ao registrar o carro.", "erro")
            return redirect(url_for('registrar_carro'))

    # GET → exibe página
    return render_template(
        'registrar_carro.html',
        current_tab='registrar_carro'
    )

     

@app.route('/pcu/meus_carros')
def meus_carros():
    pcu_id = session.get('usuario_id')

    if not pcu_id:
        flash("Erro: usuário não identificado.", "erro")
        return redirect(url_for('login'))
    
    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)

        sql = """
            SELECT id_carro, placa, modelo, porte, carroceria
            FROM tb_carro
            WHERE pcu_id = %s    
        """

        cursor.execute(sql, (pcu_id,))
        carros = cursor.fetchall()

        cursor.close()
        conexao.close()
    
    except Exception as e:
        print("ERRO AO CONSULTAR CARROS:", e)
        flash("Erro ao carregar seus carros.", "erro")
        carros = []
    
    return render_template(
        'meus_carros.html',
        carros=carros,
        current_tab='meus_carros'
    )


@app.route('/pcu/carteira')
def carteira():
    pcu_id = session.get('usuario_id')

    if not pcu_id:
        flash("Erro: usuário não identificado.", "erro")
        return redirect(url_for('login'))

    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)

        # SALDO ATUAL
        cursor.execute("""
            SELECT saldo 
            FROM tb_carteira
            WHERE pcu_id = %s
        """, (pcu_id,))
        carteira = cursor.fetchone()
        saldo = carteira['saldo'] if carteira else 0

        # TOTAL DE FRETES PAGOS
        cursor.execute("""
            SELECT COUNT(*) AS total
            FROM tb_frete
            WHERE pcu_id = %s
              AND status = 'Pago'
        """, (pcu_id,))
        total_fretes_pagos = cursor.fetchone()['total']

        # FRETES PENDENTES DE PAGAMENTO
        cursor.execute("""
            SELECT COUNT(*) AS total
            FROM tb_frete
            WHERE pcu_id = %s
              AND status = 'Aceito'
        """, (pcu_id,))
        fretes_pendentes = cursor.fetchone()['total']

        # HISTÓRICO DE MOVIMENTAÇÕES (ÚLTIMOS 10 PAGOS)
        cursor.execute("""
            SELECT 
                id_frete,
                valor,
                DATE_FORMAT(data_pagamento, '%%d/%%m/%%Y') AS data
            FROM tb_frete
            WHERE pcu_id = %s
              AND status = 'Pago'
            ORDER BY data_pagamento DESC
            LIMIT 10
        """, (pcu_id,))
        transacoes = cursor.fetchall()

        cursor.close()
        conexao.close()

    except Exception as e:
        print("ERRO CARTEIRA:", e)
        flash("Erro ao carregar carteira.", "erro")

        saldo = 0
        total_fretes_pagos = 0
        fretes_pendentes = 0
        transacoes = []

    return render_template(
        'carteira.html',
        saldo=saldo,
        total_fretes_pagos=total_fretes_pagos,
        fretes_pendentes=fretes_pendentes,
        transacoes=transacoes,
        current_tab='carteira'
    )



@app.route('/pcu/aceitar_frete/<int:frete_id>', methods=['POST'])
def aceitar_frete(frete_id):
    pcu_id = session.get('usuario_id')

    if not pcu_id:
        flash("Erro: usuário não identificado.", "erro")
        return redirect(url_for('login'))

    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)

        conexao.start_transaction()

        # Verificar se o frete ainda está pendente
        cursor.execute("""
            SELECT status
            FROM tb_frete
            WHERE id_frete = %s
            FOR UPDATE
        """, (frete_id,))

        frete = cursor.fetchone()

        if not frete or frete["status"] != "Pendente":
            flash("Frete indisponível.", "erro")
            return redirect(url_for('listar_fretes'))

        # Aceitar o frete
        cursor.execute("""
            UPDATE tb_frete
            SET status = 'Aceito',
                pcu_id = %s
            WHERE id_frete = %s
        """, (pcu_id, frete_id))

        conexao.commit()

        flash("Frete aceito com sucesso!", "sucesso")
        return redirect(url_for('meus_fretes'))
        
    except Exception as e:
        conexao.rollback()
        print("ERRO AO ACEITAR FRETE:", e)
        flash("Erro ao aceitar o frete.", "erro")
        return redirect(url_for('listar_fretes'))
    
    finally:
        cursor.close()
        conexao.close()
        

@app.route('/pcu/meus_fretes')
def meus_fretes():
    pcu_id = session.get('usuario_id')

    if not pcu_id:
        flash("Erro: usuário não identificado.", "erro")
        return redirect(url_for('login'))

    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)

        sql = """
            SELECT id_frete, origem, destino, valor, carro_necessario, status
            FROM tb_frete
            WHERE pcu_id = %s
            ORDER BY id_frete DESC
        """

        cursor.execute(sql, (pcu_id,))
        fretes = cursor.fetchall()

        cursor.close()
        conexao.close()

    except Exception as e:
        print("ERRO AO BUSCAR MEUS FRETES:", e)
        flash("Erro ao carregar fretes.", "erro")
        fretes = []

    return render_template(
        "meus_fretes.html",
        fretes=fretes,
        current_tab="meus_fretes"
    )


@app.route('/empresa/dashboard')
def dashboard_empresa():
    empresa_id = session.get('usuario_id')

    if not empresa_id:
        flash("Erro: empresa não identificada.", "erro")
        return redirect(url_for('login'))

    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)

        # Quantos fretes publicados pela empresa
        cursor.execute("""
            SELECT COUNT(*) AS total
            FROM tb_frete
            WHERE empresa_id = %s
        """, (empresa_id,))
        total_publicados = cursor.fetchone()["total"]

        # Quantos pendentes (não aceitos ainda)
        cursor.execute("""
            SELECT COUNT(*) AS total
            FROM tb_frete
            WHERE empresa_id = %s
              AND status = 'Pendente'
        """, (empresa_id,))
        pendentes = cursor.fetchone()["total"]

        # Quantos fretes aceitos (aguardando pagamento)
        cursor.execute("""
            SELECT COUNT(*) AS total
            FROM tb_frete
            WHERE empresa_id = %s
              AND status = 'Aceito'
        """, (empresa_id,))
        aceitos = cursor.fetchone()["total"]

        cursor.close()
        conexao.close()

        return render_template(
            'dashboard_empresa_base.html',
            total_publicados=total_publicados,
            pendentes=pendentes,
            aceitos=aceitos,
            current_tab='dashboard'
        )

    except Exception as e:
        print("ERRO NO DASHBOARD:", e)
        flash("Erro ao carregar dashboard.", "erro")
        return render_template('dashboard_empresa.html')

@app.route('/empresa/publicar_frete', methods=['GET', 'POST'])
def publicar_frete():
    if request.method == 'POST':
        origem = request.form.get('origem')
        destino = request.form.get('destino')
        valor = request.form.get('valor')
        carro = request.form.get('carro')

        empresa_id = session.get('usuario_id')
        if not empresa_id:
            flash("Erro: usuário não identificado.", "erro")

        if origem and destino and valor and carro:
            try:
                conexao = conectar()
                cursor = conexao.cursor()

                sql= """
                    INSERT INTO tb_frete(origem, destino,valor, empresa_id, carro_necessario, status )
                    VALUES (%s, %s, %s, %s, %s, %s)
                """
                valores = (origem, destino, valor, empresa_id, carro, "Pendente")

                cursor.execute(sql, valores)
                conexao.commit()

                cursor.close()
                conexao.close()

                flash('Frete publicado com sucesso!', 'sucesso')
                return redirect(url_for('ver_fretes_empresa'))
    
            except Exception as e:
                print("Erro AO INSERIR FRETE:", e)
                flash('Erro ao publicar frete.', 'erro')
        else:
            flash("Preencha todos os campos!", "erro")
   

    return render_template(
        'publicar_frete.html',
        current_tab='publicar_frete'
    )


@app.route('/empresa/ver_fretes')
def ver_fretes_empresa():
    empresa_id = session.get('usuario_id')

    if not empresa_id:
        flash("Erro: empresa não identificada.", "erro")
        return redirect(url_for('login'))
    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)
        sql = """
            SELECT id_frete, origem, destino, valor, carro_necessario, status
            FROM tb_frete
            WHERE empresa_id = %s
            ORDER BY id_frete DESC
        """

        cursor.execute(sql, (empresa_id,))
        fretes = cursor.fetchall()

        cursor.close()
        conexao.close()

        return render_template(
            'ver_fretes_empresa.html',
            fretes=fretes,
            current_tab='ver_fretes'
        )
    
    except Exception as e:
        print("Erro ao buscar fretes:", e)
        flash("Erro ao carregar fretes publicados.", "erro")
        return render_template('ver_fretes_empresa.html', fretes=[], current_tab='ver_fretes')


@app.route('/empresa/pagamentos')
def pagamentos_empresa():
    empresa_id = session.get('usuario_id')

    if not empresa_id:
        flash("Erro: empresa não identificada.", "erro")
        return redirect(url_for('login'))
    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)

        sql = """
            SELECT id_frete, origem, destino, valor, carro_necessario, status, pcu_id
            FROM tb_frete
            WHERE empresa_id = %s
              AND status = 'Aceito'
            ORDER BY id_frete DESC
        """

        cursor.execute(sql, (empresa_id,))
        pagamentos = cursor.fetchall()

        cursor.close()
        conexao.close()

        return render_template(
            'pagamentos_empresa.html',
            pagamentos=pagamentos,
            current_tab='pagamentos'
        )
    except Exception as e:
        print("ERRO AO CARREGAR PAGAMENTOS:", e)
        flash("Erro ao carregar pagamentos.", "erro")
        return render_template('pagamentos_empresa.html', pagamentos=[])


@app.route('/empresa/pagar_frete/<int:frete_id>', methods=['POST'])
def pagar_frete(frete_id):
    empresa_id = session.get('usuario_id')

    if not empresa_id:
        flash("Erro: empresa não identificada.", "erro")
        return redirect(url_for('login'))

    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)

        # PEGAR INFORMAÇÕES DO FRETE
        cursor.execute("""
            SELECT valor, pcu_id, status 
            FROM tb_frete
            WHERE id_frete = %s AND empresa_id = %s
            FOR UPDATE
        """, (frete_id, empresa_id))

        frete = cursor.fetchone()

        if not frete:
            raise Exception("Frete não encontrado.", "erro")
        
        if frete['status'] != 'Aceito':
            raise Exception("Frete não está em estado Aceito")
        
        valor = Decimal(frete['valor'])  # Converte para Decimal
        pcu_id = frete['pcu_id']

        if not pcu_id:
            raise Exception("Frete sem PCU vinculado.")

        # Verificar se o PCU tem uma carteira
        cursor.execute("""
            SELECT saldo
            FROM tb_carteira
            WHERE pcu_id = %s
            FOR UPDATE
        """, (pcu_id,))

        carteira = cursor.fetchone()

        if not carteira:
            cursor.execute("""
                INSERT INTO tb_carteira (saldo, pcu_id)
                VALUES (0, %s)
            """, (pcu_id,))

            # ATUALIZAR CARTEIRA DO PCU (ADICIONAR O VALOR)
        cursor.execute("""
            UPDATE tb_carteira
            SET saldo = saldo + %s
            WHERE pcu_id = %s
        """, (valor, pcu_id))

        if cursor.rowcount != 1:
            raise Exception("Falha ao atualizar carteira.")
            

        # MARCAR O FRETE COMO PAGO
        cursor.execute("""
            UPDATE tb_frete
            SET status = 'Pago',
                data_pagamento = NOW()
            WHERE id_frete = %s
        """, (frete_id,))

        # Commit para garantir que as alterações sejam salvas
        conexao.commit()


        flash("Pagamento realizado com sucesso!", "sucesso")
        return redirect(url_for('pagamentos_empresa'))

    except Exception as e:
        print("ERRO AO PAGAR FRETE:", e)
        flash("Erro ao realizar pagamento.", "erro")
        return redirect(url_for('pagamentos_empresa'))
    
    finally:
        
        cursor.close()
        conexao.close()


@app.route('/empresa/frete/editar/<int:frete_id>', methods=['GET', 'POST'])
def editar_frete(frete_id):
    empresa_id = session.get('usuario_id')

    if not empresa_id:
        flash("Erro: empresa não identificada.", "erro")
        return redirect(url_for('login'))
    
    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)

        cursor.execute("""
            SELECT id_frete, origem, destino, valor, carro_necessario, status
            FROM tb_frete
            WHERE id_frete = %s AND empresa_id = %s
        """, (frete_id, empresa_id))

        frete = cursor.fetchone()

        if not frete:
            flash("Frete não encontrado.", "erro")
            return redirect(url_for('ver_fretes_empresa'))
        
        if frete['status'] != "Pendente":
            flash("Este frete não pode ser editado porque já foi aceito ou pago.", "warning")
            return redirect(url_for('ver_fretes_empresa'))
        
        if request.method == 'POST':
            origem = request.form.get('origem')
            destino = request.form.get('destino')
            valor = request.form.get('valor')
            carro = request.form.get('carro')

            # Converter valor corretamente
            valor = valor.replace(",", ".")
            
            try:
                cursor.execute("""
                    UPDATE tb_frete
                    SET origem = %s,
                        destino = %s,
                        valor = %s,
                        carro_necessario = %s
                    WHERE id_frete = %s AND empresa_id = %s
                """, (origem, destino, valor, carro, frete_id, empresa_id))

                conexao.commit()
                flash("Frete atualizado com sucesso!", "sucesso")
                return redirect(url_for('ver_fretes_empresa'))

            except Exception as e:
                print("ERRO AO ATUALIZAR:", e)
                flash("Erro ao salvar alterações.", "erro")

        cursor.close()
        conexao.close()

        # GET → carregar página de edição
        return render_template(
            'editar_frete.html',
            frete=frete,
            current_tab='ver_fretes'
        )

    except Exception as e:
        print("ERRO NO EDITAR FRETE:", e)
        flash("Erro ao carregar dados do frete.", "erro")
        return redirect(url_for('ver_fretes_empresa'))
    
    

@app.route('/empresa/frete/excluir/<int:frete_id>', methods=['POST'])
def excluir_frete(frete_id):
    empresa_id = session.get('usuario_id')

    if not empresa_id:
        flash("Erro: empresa não identificada.", "erro")
        return redirect(url_for('login'))
    try:
        conexao = conectar()
        cursor = conexao.cursor(dictionary=True)

        cursor.execute("""
            SELECT id_frete, status
            FROM tb_frete
            WHERE id_frete = %s AND empresa_id = %s
        """, (frete_id, empresa_id))

        frete = cursor.fetchone()

        if not frete:
            flash("Frete não encontrado ou você não tem permissão para excluí-lo.", "erro")
            return redirect(url_for('ver_fretes_empresa'))

        
        if frete["status"] != "Pendente":
            flash("Este frete não pode ser excluído porque já foi aceito por um PCU.", "warning")
            return redirect(url_for('ver_fretes_empresa'))

        # EXCLUSÃO REAL
        cursor.execute("""
            DELETE FROM tb_frete
            WHERE id_frete = %s AND empresa_id = %s
        """, (frete_id, empresa_id))

        conexao.commit()

        flash(f"Frete #{frete_id} excluído com sucesso!", "success")

        cursor.close()
        conexao.close()
        return redirect(url_for('ver_fretes_empresa'))

    except Exception as e:
        print("ERRO AO EXCLUIR FRETE:", e)
        flash("Erro ao excluir o frete.", "erro")
        return redirect(url_for('ver_fretes_empresa'))
 

if __name__ == '__main__':
    app.run(debug=True)




print('oi')
